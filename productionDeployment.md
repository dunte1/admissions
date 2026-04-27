# Production Deployment Guide

## Admission Portal - Production Deployment Instructions

### 1. Upload & Extract

Upload `dunco-pro.zip` to your server (e.g., via FTP, SSH, or hosting panel).

Extract the zip contents to your web root directory (e.g., `/var/www/admission-portal` or your hosting's web directory).

### 2. Configure Environment

```bash
cd /path/to/project
cp .env_example .env
```

Edit `.env` with your production settings:

```env
APP_NAME="Admission Portal"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://admissions.duncowebsolutions.co.ke

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@admissions.duncowebsolutions.co.ke"
MAIL_FROM_NAME="${APP_NAME}"

# Cache & Queue
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 3. Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Run Migrations

```bash
php artisan migrate --force
```

### 6. Create Storage Link

```bash
php artisan storage:link
```

### 7. Clear & Cache Config

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
```

On some systems, you may need to use `sudo`:

```bash
sudo chown -R www-data:www-data /path/to/project
```

### 9. Web Server Configuration

#### For Apache (.htaccess)
Ensure `public/.htaccess` is properly configured. Laravel includes a default `.htaccess` file that handles URL rewriting.

#### For Nginx
Add a server block like this:

```nginx
server {
    listen 80;
    server_name admissions.duncowebsolutions.co.ke;
    root /path/to/project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt { access_log off; log_not_found off; }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ /\.ht {
        deny all;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ /vendor/.*\.php$ {
        deny all;
    }
}
```

---

## Troubleshooting

### 500 Internal Server Error

1. Check PHP error logs: `storage/logs/`
2. Verify `.env` configuration
3. Ensure permissions are correct on `storage/` and `bootstrap/cache/`
4. Run `php artisan config:clear` and try again

### Assets Not Loading

1. Verify `public/build/` exists with manifest.json
2. Check APP_URL in `.env` matches your actual domain
3. Run `php artisan asset:clear` if using Laravel Mix caching

### CSP (Content Security Policy) Issues

If you experience blocked scripts/styles:

1. Check `app/Http/Middleware/VerifyCsrfToken.php`
2. Check any custom CSP middleware in `app/Http/Middleware/`
3. Adjust Content-Security-Policy headers as needed

### Database Connection Errors

1. Verify DB credentials in `.env`
2. Ensure the database server is running
3. Check firewall settings allow database connections

### Permission Denied Errors

```bash
# Owner permissions
sudo chown -R www-data:www-data /path/to/project

# Directory permissions
find /path/to/project -type d -exec chmod 775 {} \;

# File permissions  
find /path/to/project -type f -exec chmod 664 {} \;

# Storage and cache must be writable
chmod -R 775 storage bootstrap/cache
```

---

## Security Checklist

- [ ] APP_DEBUG=false in production
- [ ] Strong APP_KEY generated
- [ ] Database credentials are unique
- [ ] HTTPS enforced
- [ ] File permissions restricted
- [ ] Unnecessary files removed from public/
- [ ] Regular backups configured
- [ ] SSL certificate installed

---

## Support

For issues, check:
- Laravel logs: `storage/logs/laravel.log`
- Web server error logs
- PHP error logs

Generated: April 2026
