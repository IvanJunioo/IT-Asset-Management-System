#!/bin/bash
DIR_NAME="itassets"   # must be the same as in install.sh

export $(grep -v '^#' .env | xargs)

read -p "Are you sure you want to uninstall $DIR_NAME and DELETE all system files? (y/n): " confirm
if [ "$confirm" != "y" ]; then
    echo "Uninstallation cancelled."
    exit 1
fi

# Drop database tables and admin user
sudo mysql -e "DROP DATABASE IF EXISTS $DB_NAME;"
# sudo mysql -e "DROP USER IF EXISTS '$DB_USER'@'$DB_HOST';"

# Delete the config files
sudo rm /etc/nginx/sites-enabled/$DIR_NAME
sudo rm /etc/nginx/sites-available/$DIR_NAME
sudo ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default   # Re-enable default nginx site
sudo service nginx restart

# Delete Git repo files (including this script)
sudo rm -rf /var/www/$DIR_NAME

echo "Uninstallation complete for $DIR_NAME."
