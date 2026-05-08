#!/bin/bash

# Load environment variables
if [ -f .env ]; then
  set -a
  source .env
  set +a
else
  echo "ERROR: .env file not found. Cannot proceed with clean uninstallation."
  exit 1
fi

read -p "Are you sure you want to uninstall $DIR_NAME and DELETE all system files? (y/n): " confirm
if [ "$confirm" != "y" ]; then
  echo "Uninstallation cancelled."
  exit 1
fi

# Remove Cron Job
(sudo crontab -l 2>/dev/null | grep -v "db_backup.sh") | sudo crontab -

# Drop database tables and admin user
sudo mysql -e "DROP DATABASE IF EXISTS $DB_NAME;"
# sudo mysql -e "DROP USER IF EXISTS '$DB_USER'@'$DB_HOST';"

# Clean up NGINX config
sudo rm /etc/nginx/sites-enabled/$DIR_NAME
sudo rm /etc/nginx/sites-available/$DIR_NAME
if [ -f /etc/nginx/sites-available/default ]; then
  sudo ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default     # Re-enable default site
fi
sudo service nginx restart

# Delete project files (including this script)
sudo rm -rf /var/www/$DIR_NAME

echo "Uninstallation complete for $DIR_NAME."
