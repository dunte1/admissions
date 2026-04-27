#!/bin/bash

# Database Backup Script for Admission Portal
# Run this via cron: 0 2 * * * /path/to/backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/admission-portal"
DB_NAME="admission_portal"
DB_USER="root"
DB_PASS=""
RETENTION_DAYS=30

# Create backup directory if not exists
mkdir -p $BACKUP_DIR

# MySQL Backup
mysqldump -u $DB_USER ${DB_PASS:+-p$DB_PASS} $DB_NAME | gzip > "$BACKUP_DIR/db_${DATE}.sql.gz"

# Upload to S3 (optional - uncomment if using AWS)
# aws s3 cp "$BACKUP_DIR/db_${DATE}.sql.gz" s3://your-bucket/backups/

# Remove old backups
find $BACKUP_DIR -name "db_*.sql.gz" -mtime +$RETENTION_DAYS -delete

echo "Backup completed: db_${DATE}.sql.gz"
