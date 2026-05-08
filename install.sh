#!/bin/bash

# Define local system variables
LOCAL_DIR=$(pwd)
GITHUB_REPO="https://github.com/IvanJunioo/IT-Asset-Management-System.git"

# Set up the environment variables
if [ ! -f "$LOCAL_DIR/.env" ]; then
  echo "ERROR: .env file does not exist."
  exit 1
fi

# Export environment variables
set -a
source "$LOCAL_DIR/.env"
set +a

# Check all required env vars 
REQUIRED_VARS=("DIR_NAME" "DB_NAME" "DB_USER" "DB_HOST" "ADMIN_EMAIL" "GOOGLE_CLIENT_ID" "GOOGLE_CLIENT_SECRET" "APP_PORT" "APP_DOMAIN")
for var in "${REQUIRED_VARS[@]}"; do
  if [ -z "${!var}" ]; then
    echo "ERROR: The variable '$var' is empty in your .env file."
    echo "Please fill in all values and run the script again."
    exit 1
  fi
done

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
GLOBAL_DIR="/var/www/$DIR_NAME"
sudo mkdir -p $GLOBAL_DIR						     # Make new directory
sudo chown -R $USER:$USER $GLOBAL_DIR     # Own the directory
if [ -z "$(ls -A $GLOBAL_DIR)" ]; then    # Clone the project repo if empty
  echo "Cloning Git repo $GITHUB_REPO into $DIR_NAME"
  git clone $GITHUB_REPO $GLOBAL_DIR
fi
cd $GLOBAL_DIR                            # Switch to project directory
cp "$LOCAL_DIR/.env" .env                        # Copy .env file to server
sed -i 's/\r$//' .env                            # Clean copied .env file line endings

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

# Setup database and superadmin user
sudo service mysql start
sudo mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"
sudo mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'$DB_HOST' IDENTIFIED BY '$DB_PASS';"
sudo mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'$DB_HOST';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Import schema and add superadmin account
sudo mysql $DB_NAME < db/schema.sql
sudo mysql $DB_NAME -e "INSERT INTO employee (EmpMail, FName, LName, Privilege, ActiveStatus) VALUES ('$ADMIN_EMAIL', 'Super', 'Admin', 'SuperAdmin', 'Active');"

# Setup automated backups
BACKUP_DIR="$GLOBAL_DIR/backups/db"
BACKUP_SCRIPT="$GLOBAL_DIR/db_backup.sh"
RESTORE_SCRIPT="$GLOBAL_DIR/db_restore.sh"
LOG_FILE="$BACKUP_DIR/db_backup.log"
BACKUP_SCHEDULE="0 3 * * *"                 # 3:00 AM daily increments

sudo mkdir -p "$BACKUP_DIR"
sudo chown -R $USER:$USER "$BACKUP_DIR"

# Create backup script
cat > "$BACKUP_SCRIPT" << EOF
#!/bin/bash

BASE_DIR="$GLOBAL_DIR"
set -a
source "\$BASE_DIR/.env"
set +a

BACKUP_DIR="\$BASE_DIR/backups/db"
DATE=\$(date +"%Y-%m-%d_(%I-%M_%p)")

mkdir -p "\$BACKUP_DIR"

mysqldump \
  -h "\$DB_HOST" \
  -u "\$DB_USER" \
  --password="\$DB_PASS" \
  --no-tablespaces \
  "\$DB_NAME" \
| gzip > "\$BACKUP_DIR/\${DB_NAME}_\${DATE}.sql.gz"

# Store only 7 days of backups
find "\$BACKUP_DIR" -type f -mtime +7 -delete
EOF

# create restore script
cat > "$RESTORE_SCRIPT" << EOF
#!/bin/bash

BASE_DIR="$GLOBAL_DIR"
set -a
source "\$BASE_DIR/.env"
set +a

BACKUP_DIR="\$BASE_DIR/backups/db"

echo "Available backups:"
select FILE in "\$BACKUP_DIR"/*.sql.gz; do
    if [[ -n "\$FILE" ]]; then
        echo "Restoring from: \$FILE"
        gunzip < "\$FILE" | mysql -u "\$DB_USER" --password="\$DB_PASS" "\$DB_NAME"
        echo "Restore complete."
        break
    fi
done
EOF

# make executable
chmod +x "$BACKUP_SCRIPT"
chmod +x "$RESTORE_SCRIPT"

# make the script run every 24 hours
(crontab -l 2>/dev/null | grep -v "$BACKUP_SCRIPT"; echo "$BACKUP_SCHEDULE $BACKUP_SCRIPT >> $LOG_FILE 2>&1") | crontab -

# Create an initial backup
bash "$BACKUP_SCRIPT"

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

# Serve files
sudo chown -R www-data:www-data $GLOBAL_DIR
sudo chmod -R 755 $GLOBAL_DIR

echo "Installation complete for $DIR_NAME!"
