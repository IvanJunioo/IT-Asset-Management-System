#!/bin/bash
export $(grep -v '^#' .env | xargs)

read -p "Are you sure you want to uninstall $APP_DOMAIN and DELETE all files? (y/n): " confirm
if [ "$confirm" != "y" ]; then
    echo "Uninstallation cancelled."
    exit 1
fi

# Drop database tables and admin user
sudo mysql -e "DROP DATABASE IF EXISTS $DB_NAME;"
sudo mysql -e "DROP USER IF EXISTS '$DB_USER'@'$DB_HOST';"

# Delete the config files
sudo rm /etc/nginx/sites-enabled/$APP_DOMAIN
sudo rm /etc/nginx/sites-available/$APP_DOMAIN
sudo ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/   # Re-enable default nginx site
sudo service nginx restart

# Uninstall software dependencies (optional)
# sudo apt purge nginx mysql-server php-fpm -y
# sudo apt autoremove -y

# Delete Git repo files (including this script)
sudo rm -rf /var/www/$APP_DOMAIN

echo "Uninstallation complete for $APP_DOMAIN."
