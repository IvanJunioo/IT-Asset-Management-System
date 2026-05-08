#!/bin/bash

BASE_DIR="$(pwd)"
BACKUP_DIR="$BASE_DIR/backups/db"

mkdir -p "$BACKUP_DIR"

BACKUP_SCRIPT="$BASE_DIR/db_backup.sh"
RESTORE_SCRIPT="$BASE_DIR/db_restore.sh"
LOG_FILE="$BACKUP_DIR/db_backup.log"

# create backup script
cat > "$BACKUP_SCRIPT" << EOF
#!/bin/bash

BASE_DIR="\$(pwd)"
set -a
source "\$BASE_DIR/.env"
set +a

BACKUP_DIR="\$BASE_DIR/backups/db"
DATE=\$(date +"%Y-%m-%d_(%I-%M_%p)")

mkdir -p "\$BACKUP_DIR"

mysqldump \
  -h "\$DB_HOST" \
  -u "\$DB_USER" \
  -p"\$DB_PASS" \
  --no-tablespaces \
  "\$DB_NAME" \
| gzip > "\$BACKUP_DIR/\${DB_NAME}_\${DATE}.sql.gz"

find "\$BACKUP_DIR" -type f -mtime +7 -delete
EOF

# create restore script
cat > "$RESTORE_SCRIPT" << EOF
#!/bin/bash

BASE_DIR="\$(pwd)"
set -a
source "\$BASE_DIR/.env"
set +a

BACKUP_DIR="\$BASE_DIR/backups/db"

echo "Available backups:"
select FILE in "\$BACKUP_DIR"/*.sql.gz; do
    if [[ -n "\$FILE" ]]; then
        echo "Selected: \$FILE"
        break
    fi
done

gunzip < "\$FILE" | mysql -u "\$DB_USER" -p "\$DB_NAME"
echo "Restore complete."
EOF

# make executable
chmod +x "$BACKUP_SCRIPT"
chmod +x "$RESTORE_SCRIPT"


# make the script run every 12 hours
(crontab -l 2>/dev/null | grep -v "$BACKUP_SCRIPT"; \
echo "0 3 * * * cd $BASE_DIR && $BACKUP_SCRIPT >> $LOG_FILE 2>&1") | crontab -

echo "Setup complete!"
crontab -l