#!/bin/bash

# Load Environment Variables
PROJECT_DIR="/var/www/itassets"
if [ -f "$PROJECT_DIR/.env" ]; then
  source "$PROJECT_DIR/.env"
else
  echo "Error: .env file not found at $PROJECT_DIR"
  exit 1
fi

# Define Backup Paths
FULL_BACKUP="$PROJECT_DIR/backups/full/full_weekly.sql"
INC_DIR="$PROJECT_DIR/backups/incremental"

# Confirmation Prompt
echo "WARNING: This will overwrite the current '$DB_NAME' database."
read -p "Are you sure you want to proceed? (y/n): " confirm
if [[ $confirm != [yY] ]]; then
  echo "Restore cancelled."
  exit 1
fi

# Restore Full Backup (The Foundation)
if [ -f "$FULL_BACKUP" ]; then
  echo "Restoring Full Backup: $FULL_BACKUP..."
  mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$FULL_BACKUP"
else
  echo "Error: Full backup file not found!"
  exit 1
fi

# Restore Incremental Logs (The Bricks)
# We check if there are any files matching the pattern
if ls "$INC_DIR"/mysql-bin.[0-9]* 1> /dev/null 2>&1; then
  echo "Replaying incremental logs from $INC_DIR..."
  # Replay all logs in chronological order
  mysqlbinlog "$INC_DIR"/mysql-bin.[0-9]* | mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME"
  echo "Incremental logs applied successfully."
else
  echo "No incremental logs found to apply."
fi

echo "Restore Complete!"
