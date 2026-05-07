#!/bin/bash

# Directories
PROJECT_DIR="/var/www/itassets"
FULL_DIR="$PROJECT_DIR/backups/full"
INC_DIR="$PROJECT_DIR/backups/incremental"
mkdir -p "$FULL_DIR" "$INC_DIR"

# Load variables
if [ -f "$PROJECT_DIR/.env" ]; then
  source "$PROJECT_DIR/.env"
else
  echo ".env file not found"
  exit 1
fi

# If today is Sunday, run a Full Backup to reset the chain
if [ "$(date +%u)" -eq 7 ]; then
  mysqldump -u "$DB_USER" -p"$DB_PASS" --flush-logs --delete-master-logs --single-transaction "$DB_NAME" > "$FULL_DIR/full_weekly.sql"
  # Clear out old incrementals since they are now included in the full dump
  rm -f "$INC_DIR"/*
else
  # Just an incremental backup: Flush logs and copy new ones
  mysqladmin -u "$DB_USER" -p"$DB_PASS" flush-logs

  # Copy all binary logs except the last one (which is the active one)
  LOGS=$(ls /var/log/mysql/mysql-bin.[0-9]* | head -n -1)
  if [ -n "$LOGS" ]; then   # Only copy closed logs
    cp $LOGS "$INC_DIR/"
  fi
fi
