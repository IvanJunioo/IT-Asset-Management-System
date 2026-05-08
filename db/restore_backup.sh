#!/bin/bash

# Load environment variables
PROJECT_DIR="/var/www/itassets"
if [ -f "$PROJECT_DIR/.env" ]; then
  source "$PROJECT_DIR/.env"
else
  echo "Error: .env file not found at $PROJECT_DIR"
  exit 1
fi

# Define backup paths
FULL_BACKUP="$PROJECT_DIR/backups/full/full_weekly.sql"
INC_DIR="$PROJECT_DIR/backups/incremental"

# Confirmation prompt
echo "WARNING: This will overwrite the current '$DB_NAME' database."
read -p "Are you sure you want to proceed? (y/n): " confirm
if [[ $confirm != [yY] ]]; then
  echo "Database restoration cancelled."
  exit 1
fi

# Restore full backup
if [ -f "$FULL_BACKUP" ]; then
  echo "Restoring full backup: $FULL_BACKUP..."
  mysql -u "$DB_USER" --password="$DB_PASS" "$DB_NAME" < "$FULL_BACKUP"
else
  echo "Error: Full backup file was not found!"
  exit 1
fi

# Restore incremental logs
if ls "$INC_DIR"/mysql-bin.[0-9]* 1> /dev/null 2>&1; then # Check if there are any files matching the bin logs pattern
  echo "Replaying incremental logs from $INC_DIR..."
  # Replay all logs in chronological order
  mysqlbinlog "$INC_DIR"/mysql-bin.[0-9]* | mysql -u "$DB_USER" --password="$DB_PASS" "$DB_NAME"
  echo "Incremental logs applied successfully."
else
  echo "No incremental logs found to apply."
fi

echo "Database restoration completed!"
