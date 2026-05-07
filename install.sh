#!/bin/bash

# Define local system variables
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

# Set up Linux directory and load Github repo
sudo mkdir -p /var/www/$DIR_NAME						      # Make new directory
sudo chown -R $USER:$USER /var/www/$DIR_NAME     # Own the directory
if [ -z "$(ls -A /var/www/$DIR_NAME)" ]; then    # Clone the project repo if empty
  git clone $GITHUB_REPO /var/www/$DIR_NAME
fi
cd /var/www/$DIR_NAME                            # Switch to project directory

# Set up the environment file if nonexistent
if [ ! -f .env ]; then
  echo "Creating .env from template..."
  cp .env.example .env
  EDIT_ENV="y"
else
  echo ".env file already exists."
  read -p "Do you want to edit your existing configuration? (y/n): " EDIT_ENV
fi

if [ "$EDIT_ENV" = "y" ]; then
  echo "--------------------------------------------------------"
  echo "ACTION REQUIRED: Opening .env for configuration."
  echo "Please set your DB_USER, DB_PASS, and DB_NAME."
  echo "Press Enter to start editing, then Ctrl+O, Enter, Ctrl+X to save and exit."
  echo "--------------------------------------------------------"
  read -p "Press [Enter] to continue..."
  nano .env
fi

# Load variables from .env file
export $(grep -v '^#' .env | xargs)

for var in "${REQUIRED_VARS[@]}"; do
  if [ -z "${!var}" ]; then
    echo "ERROR: The variable '$var' is empty in your .env file."
    echo "Please run the script again and fill in all values."
    exit 1
  fi
done

# Configure NGINX 
sudo bash -c "cat <<EOF > /etc/nginx/sites-available/$DIR_NAME
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

# Setup database and admin user
sudo service mysql start
sudo mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"
sudo mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'$DB_HOST' IDENTIFIED BY '$DB_PASS';"
sudo mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'$DB_HOST';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Import schema
sudo mysql $DB_NAME < db/schema.sql

echo "Database $DB_NAME has been set up successfully for $DB_USER"

# Setup composer
if ! command -v composer >/dev/null 2>&1; then    # Install composer if not yet installed
  echo "Composer not found. Installing now..."
  curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
fi

# Install project external dependencies
composer install --no-dev --optimize-autoloader

echo "Installation Complete for $DIR_NAME!"
