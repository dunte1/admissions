# KMTC Admission Portal - Deployment Guide

## Production Deployment Guide

This document provides comprehensive instructions for deploying the KMTC Admission Portal to a production server.

**Project Start Date:** March 13, 2027

---

## 1. Server Requirements

### Minimum Requirements
- **PHP**: 8.1 or higher
- **Web Server**: Apache (with mod_rewrite) or Nginx
- **Database**: MySQL 8.0+ or PostgreSQL 13+ or SQLite 3
- **Composer**: 2.x
- **Node.js**: 18.x or higher (for frontend builds)
- **npm**: 9.x or higher

### PHP Extensions Required
- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- Intl PHP Extension
- MBstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- ZIP PHP Extension
- GD PHP Extension (for image manipulation)
- EXIF PHP Extension

---

## 2. Installation Steps

### Step 1: Upload Files
Upload the ZIP package contents to your web server's public HTML directory (e.g., `/var/www/html/` or `/public_html/`).

### Step 2: Extract Files
```bash
unzip admission-portal-production.zip -d /var/www/html/
cd /var/www/html
```

### Step 3: Create Symbolic Link for Storage
```bash
php artisan storage:link
```

If the above fails, manually create the symbolic link:
```bash
ln -s /var/www/html/storage/app/public /var/www/html/public/storage
```

---

## 3. Environment Configuration

### Step 1: Copy Environment File
```bash
cp .env.example .env
```

### Step 2: Generate Application Key
```bash
php artisan key:generate
```

### Step 3: Configure .env File
Edit the `.env` file with your production settings:

```env
APP_NAME="KMTC Admissions Portal"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_secure_password

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="admissions@kmtc.ac.ke"
MAIL_FROM_NAME="${APP_NAME}"

# Redis (if using)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# M-Pesa Configuration (Kenyan Payments)
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=your_shortcode
MPESA_PASSKEY=your_passkey
MPESA_CALLBACK_URL=https://your-domain.com/api/mpesa/callback
MPESA_ENVIRONMENT=production
```

---

## 4. Database Setup

### Create Database
```sql
CREATE DATABASE admission_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'admission_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON admission_portal.* TO 'admission_user'@'localhost';
FLUSH PRIVILEGES;
```

### Run Migrations
```bash
php artisan migrate --force
```

### Seed Initial Data (Optional)
```bash
php artisan db:seed --force
```

---

## 5. Permissions Configuration

### Set Directory Permissions
```bash
# Storage and cache directories
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# For shared hosting (if 775 doesn't work)
chmod -R 777 storage/
chmod -R 777 bootstrap/cache/
```

### Set File Permissions
```bash
chmod 644 .env
chmod 644 artisan
```

---

## 6. Queue Setup

### Install Supervisor (Recommended for Production)
```bash
sudo apt-get install supervisor
```

### Create Supervisor Configuration
```bash
sudo nano /etc/supervisor/conf.d/admission-portal.conf
```

Add the following configuration:
```ini
[program:admission-portal-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
stopwaitsecs=3600
```

### Start Supervisor
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start admission-portal-worker:*
```

---

## 7. Cron Jobs Setup

### Add Laravel Scheduler to Crontab
```bash
crontab -e
```

Add the following line:
```cron
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

This runs the scheduler every minute to handle:
- Sending scheduled notifications
- Cleaning up expired trials
- Processing subscription renewals
- Generating reports

---

## 8. Web Server Configuration

### Apache (Virtual Host)
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/admission-portal-error.log
    CustomLog ${APACHE_LOG_DIR}/admission-portal-access.log combined

    <IfModule mod_ssl.c>
        SSLEngine on
        SSLCertificateFile /path/to/fullchain.pem
        SSLCertificateKeyFile /path/to/privkey.pem
    </IfModule>
</VirtualHost>
```

### Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ /\.(jpg|jpeg|gif|png|svg|ico|css|js|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

---

## 9. HTTPS Configuration

### Using Let's Encrypt (Recommended)
```bash
sudo apt-get install certbot python3-certbot-apache
sudo certbot --apache -d your-domain.com -d www.your-domain.com
```

Or for Nginx:
```bash
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

### Force HTTPS
Add to `public/index.php` before session start:
```php
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    URL::forceScheme('https');
}
```

---

## 10. Cache Optimization

After deployment, clear and rebuild caches:
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild production caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Rebuild the autoloader
composer dump-autoload --optimize
```

---

## 11. Scheduled Maintenance

### Create Maintenance Script
```bash
#!/bin/bash
# maintenance.sh

cd /var/www/html

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Clear old logs (keep last 7 days)
find storage/logs -name "*.log" -mtime +7 -delete

# Clear old debugbar files
rm -rf storage/debugbar/*

# Clear old compiled views
rm -rf storage/framework/views/*

echo "Maintenance completed at $(date)"
```

Make executable:
```bash
chmod +x maintenance.sh
```

Run weekly:
```bash
# Add to crontab
0 2 * * 0 /var/www/html/maintenance.sh >> /var/www/html/storage/logs/maintenance.log 2>&1
```

---

## 12. Troubleshooting

### Common Issues

#### 500 Internal Server Error
- Check PHP error logs: `/var/log/apache2/error.log` or `/var/log/nginx/error.log`
- Ensure `.env` file exists and has correct permissions
- Verify storage directories are writable
- Check if required PHP extensions are installed

#### Database Connection Error
- Verify database credentials in `.env`
- Ensure MySQL/PostgreSQL service is running
- Check if database user has proper permissions
- Test connection: `php artisan tinker` then `DB::connection()->getPdo()`

#### Session Not Working
- Ensure `storage/framework/sessions/` is writable
- Check SESSION_DRIVER in `.env`
- For Redis: verify Redis connection

#### Queue Not Processing
- Check if Supervisor is running: `sudo supervisorctl status`
- Check worker logs: `storage/logs/worker.log`
- Restart workers: `sudo supervisorctl restart admission-portal-worker:*`

#### Assets Not Loading
- Verify `public/build` exists with compiled assets
- Run: `npm run build`
- Check if storage link exists: `ls -la public/storage`

### Testing Deployment

```bash
# Check if artisan works
php artisan --version

# Test route caching
php artisan route:list

# Test configuration
php artisan config:show app

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo()
```

---

## 13. Security Checklist

- [ ] Set `APP_DEBUG=false` in production
- [ ] Use strong database passwords
- [ ] Enable HTTPS (SSL certificate)
- [ ] Set proper file permissions (644 for files, 755 for directories)
- [ ] Protect `.env` file (should not be web-accessible)
- [ ] Disable directory listing (`Options -Indexes`)
- [ ] Set secure session cookies
- [ ] Configure firewall (only allow HTTP/HTTPS/SSH)
- [ ] Regular backups enabled
- [ ] Monitor error logs regularly

---

## 14. Backup Strategy

### Database Backup
```bash
mysqldump -u admission_user -p admission_portal > backup_$(date +%Y%m%d).sql
```

### Full Backup Script
```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR=/path/to/backups
APP_DIR=/var/www/html

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u admission_user -p'admission_password' admission_portal > $BACKUP_DIR/db_$DATE.sql

# Files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C $APP_DIR .

# Remove backups older than 30 days
find $BACKUP_DIR -mtime +30 -delete

echo "Backup completed: $DATE"
```

Schedule daily backups:
```cron
0 3 * * * /path/to/backup.sh >> /var/log/backups.log 2>&1
```

---

## 15. Monitoring

### Health Check Endpoint
Access `/health` to verify system status (ensure this route exists).

### Log Monitoring
```bash
# Apache/Nginx errors
tail -f /var/log/apache2/error.log

# Application logs
tail -f storage/logs/laravel.log

# Queue worker logs
tail -f storage/logs/worker.log
```

---

## Support

For technical support, contact:
- Email: support@kmtc.ac.ke
- Documentation: https://docs.kmtc.ac.ke

---

*Version 1.0 | Last Updated: July 2026*
