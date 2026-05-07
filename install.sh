#!/bin/bash

# Define local system variables
LOCAL_DIR=$(pwd)
DIR_NAME="itassets"
GITHUB_REPO="https://github.com/IvanJunioo/IT-Asset-Management-System.git"
REQUIRED_VARS=("DB_NAME" "DB_USER" "GOOGLE_CLIENT_ID" "GOOGLE_CLIENT_SECRET" "APP_PORT" "APP_DOMAIN")

# Install software dependencies
sudo apt update
sudo apt install php-fpm -y         # PHP FastCGI Process Manager
sudo apt install php-cli -y         # PHP Command Line Interface
sudo apt install php-mbstring -y    # PHP Multibyte string funcs
sudo apt install curl -y            # Data transfer
sudo apt install unzip -y           # ZIP Extraction
sudo apt install nginx -y           # Proxy web server
sudo apt install mysql-server -y    # Database
sudo apt install git -y             # Version control
sudo apt install cron -y            # Task scheduler

# Set up Linux directory and load Github repo
sudo mkdir -p /var/www/$DIR_NAME						     # Make new directory
sudo chown -R $USER:$USER /var/www/$DIR_NAME     # Own the directory
if [ -z "$(ls -A /var/www/$DIR_NAME)" ]; then    # Clone the project repo if empty
  echo "Cloning Git repo $GITHUB_REPO into $DIR_NAME"
  git clone $GITHUB_REPO /var/www/$DIR_NAME
fi
cd /var/www/$DIR_NAME                            # Switch to project directory

# Set up the environment variables
if [ ! -f "$LOCAL_DIR/.env" ]; then
  echo "ERROR: .env file does not exist."
  exit 1
fi
cp "$LOCAL_DIR/.env" .env   # Copy .env file to server
sed -i 's/\r$//' .env       # Clean copied .env file line endings

set -a
source .env
set +a

# Check all required env vars 
for var in "${REQUIRED_VARS[@]}"; do
  if [ -z "${!var}" ]; then
    echo "ERROR: The variable '$var' is empty in your .env file."
    echo "Please fill in all values and run the script again."
    exit 1
  fi
done

# Configure MySQL
CONF_FILE="/etc/mysql/mysql.conf.d/mysqld.cnf"
sudo sed -i "s/^log_bin/#log_bin/" $CONF_FILE     # Avoid clashing logs by commenting old ones
sudo bash -c "cat <<'EOF' >> $CONF_FILE
# Incremental Backup Config
server-id        = 1
log_bin          = /var/log/mysql/mysql-bin.log
expire_logs_days = 7
max_binlog_size  = 100M
EOF"
sudo systemctl restart mysql

# Setup database and admin user
sudo service mysql start
sudo mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"
sudo mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'$DB_HOST' IDENTIFIED BY '$DB_PASS';"
sudo mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'$DB_HOST';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Import schema
sudo mysql $DB_NAME < db/schema.sql

# Create initial Full Backup and directory
BACKUP_DIR="/var/www/$DIR_NAME/backups/full"
sudo mkdir -p "$BACKUP_DIR"
sudo chown -R $USER:$USER "$BACKUP_DIR"
echo "Creating initial full backup..."
sudo mysqldump -u "$DB_USER" -p"$DB_PASS" --flush-logs --single-transaction --databases "$DB_NAME" > "$BACKUP_DIR/full_weekly.sql"

# Setup automated backups
BACKUP_RUNNER="/usr/local/bin/mysql_backup.sh"
BACKUP_SCHEDULE="0 3 * * *"                 # 3:00 AM daily increments
sudo cp db/mysql_backup.sh $BACKUP_RUNNER   # Copy the script to a system binary folder for easier execution
sudo chmod +x $BACKUP_RUNNER                # Make the script executable
sudo systemctl enable cron
sudo systemctl start cron
(sudo crontab -l 2>/dev/null | grep -v "mysql_backup.sh"; echo "${BACKUP_SCHEDULE} ${BACKUP_RUNNER}") | sudo crontab -

echo "Database $DB_NAME has been set up successfully for user: $DB_USER. Incremental backups scheduled with: $BACKUP_SCHEDULE"

# Configure NGINX 
sudo bash -c "cat <<'EOF' > /etc/nginx/sites-available/$DIR_NAME
server {
    listen $APP_PORT;
    server_name $APP_DOMAIN;
    root /var/www/$DIR_NAME/public;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
    }
}
EOF"                          

# Enable site and cleanup
sudo ln -sf /etc/nginx/sites-available/$DIR_NAME /etc/nginx/sites-enabled/$DIR_NAME       # link and enable the new site
sudo rm -f /etc/nginx/sites-enabled/default					                                      # remove default active site
sudo nginx -t									                                                            # test config
sudo service nginx restart							                                                  # restart nginx

# Setup composer
if ! command -v composer >/dev/null 2>&1; then    # Install composer if not yet installed
  echo "Composer not found. Installing now..."
  curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
fi

# Install project external dependencies
composer install --no-dev --optimize-autoloader

echo "Installation complete for $DIR_NAME!"
