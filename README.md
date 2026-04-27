# KMTC Admissions Portal

A premium, fully functional school/college admission portal built with Laravel 11, designed to streamline applications, payments, and student tracking for educational institutions.

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Architecture Overview](#architecture-overview)
- [Installation](#installation)
- [Environment Setup](#environment-setup)
- [Quick Start](#quick-start)
- [AI Assistant](#ai-assistant-eliana-d)
- [Multi-Tenant System](#multi-tenant-system)
- [Security](#security)
- [API Endpoints](#api-endpoints)

## Features

### Student Portal
- Student registration with email verification
- Secure password hashing with bcrypt
- Login/logout functionality
- Password reset via email
- Two-factor authentication support
- Multi-language support (English + Kiswahili)
- Profile management
- Application history and status tracking
- Real-time notifications

### Admission Application Form
- Multi-step dynamic forms (Personal Info, Academic Background, Guardian Info, Documents)
- File uploads with validation
- Auto-save draft applications
- Conditional fields
- Online payment integration (M-PESA, PayPal, Visa/MasterCard)
- PDF generation for applications

### Admin Dashboard
- View all applications with filters
- Approve, reject, or request more information
- Bulk actions (export to PDF/Excel)
- Analytics: total applications, pending, approved, rejected
- Automated email/SMS notifications
- Role-based access (Admin, Registrar, Accountant, Reviewer, Support)

### AI Assistant - Eliana D
- **Sales Mode**: Guided admission assistance for visitors
- **Support Mode**: Context-aware help for logged-in users
- **Hybrid Mode**: Combination of both
- Knowledge base management (FAQs, documents, policies)
- Web crawling for automatic knowledge extraction
- Human handoff with escalation notifications
- Embeddable widget for external websites
- AI-guided admission flow

### Security & Performance
- CSRF/XSS protection
- Input validation
- Rate limiting
- Audit logs
- Session management
- Redis caching
- Queue processing for heavy tasks

## Tech Stack

- **Backend:** Laravel 11 (PHP 8.2+)
- **Database:** MySQL 8.0
- **Cache/Queue:** Redis
- **Frontend:** Tailwind CSS, Alpine.js
- **Authentication:** Laravel Session/Sanctum
- **File Storage:** Local/S3
- **AI:** OpenRouter API

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                     Load Balancer (Optional)                │
└─────────────────────────┬───────────────────────────────────┘
                          │
┌─────────────────────────▼───────────────────────────────────┐
│                     Nginx Web Server                         │
└─────────────────────────┬───────────────────────────────────┘
                          │
┌─────────────────────────▼───────────────────────────────────┐
│                   Laravel Application                        │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐  │
│  │  Web Routes │  │   API Routes │  │  WebSocket (Pusher) │  │
│  └─────────────┘  └─────────────┘  └─────────────────────┘  │
│  ┌─────────────────────────────────────────────────────────┐│
│  │              Multi-Tenant Middleware                    ││
│  │  - School Scope (data isolation)                         ││
│  │  - Role & Permission Check                              ││
│  │  - Subscription Verification                            ││
│  │  - Two-Factor Authentication                            ││
│  └─────────────────────────────────────────────────────────┘│
└─────────────────────────┬───────────────────────────────────┘
                          │
         ┌────────────────┼────────────────┐
         │                │                │
┌────────▼────────┐ ┌────▼────┐ ┌────────▼────────┐
│   MySQL DB      │ │  Redis  │ │   File Storage  │
│  (Per Tenant)   │ │ (Cache/ │ │   (S3/Local)    │
│                 │ │  Queue) │ │                 │
└─────────────────┘ └─────────┘ └─────────────────┘
```

### Multi-Tenant Design

- Each school has isolated data with `school_id` foreign keys
- Super admin manages global settings and school provisioning
- Schools can have custom branding (colors, logo, tagline)
- Subscription-based access control per school

## Installation

### Prerequisites

- PHP 8.2+
- Composer 2.x
- Node.js 18+
- MySQL 8.0+
- Redis 7.x
- Git

### Local Development

```bash
# Clone the repository
git clone https://github.com/your-repo/admission-portal.git
cd admission-portal

# Install PHP dependencies
composer install

# Install frontend dependencies
npm install

# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create database
mysql -u root -p -e "CREATE DATABASE admission_portal;"

# Configure .env file
# DB_DATABASE=admission_portal
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Seed the database (optional)
php artisan db:seed

# Create storage link
php artisan storage:link

# Build assets
npm run dev

# Start development server
php artisan serve
```

## Environment Setup

### Required .env Variables

```env
# Application
APP_NAME="Admission Portal"
APP_ENV=local
APP_DEBUG=false
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=admission_portal
DB_USERNAME=root
DB_PASSWORD=

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# M-PESA (Kenyan Mobile Money)
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=your_shortcode
MPESA_PASSKEY=your_passkey
MPESA_CALLBACK_URL=https://yourdomain.com/payment/callback
MPESA_ENVIRONMENT=sandbox

# PayPal
PAYPAL_CLIENT_ID=your_client_id
PAYPAL_CLIENT_SECRET=your_client_secret
PAYPAL_MODE=sandbox

# AI Assistant (OpenRouter)
OPENROUTER_API_KEY=your_api_key
OPENROUTER_MODEL=mistralai/mistral-7b-instruct

# SMS (Vonage)
VONAGE_API_KEY=your_api_key
VONAGE_API_SECRET=your_api_secret
VONAGE_SMS_FROM=YOURApp
```

## Quick Start

### 1. Access the Application

- **Landing Page**: `http://localhost`
- **Login**: `http://localhost/login`
- **Register**: `http://localhost/register`

### 2. Create Your First School (Super Admin)

1. Login with super admin credentials (seeded)
2. Navigate to **Schools → Create School**
3. Configure:
   - School name
   - Contact information
   - Branding (colors, logo)
   - Subscription plan
4. Create admin user for the school

### 3. Configure AI Assistant

1. Go to **AI Settings** in super admin or school admin
2. Set AI name: "Eliana D"
3. Configure welcome message and tone (professional/friendly/formal)
4. Add knowledge base items (FAQs, documents)
5. Generate embed code for external websites
6. Enable/disable features as needed

### 4. Set Up Programs

1. Create departments
2. Add programs with:
   - Name, description, level
   - Duration
   - Intake periods
   - Application fee
3. Configure application form fields

### 5. Launch

- Students can apply through the portal
- Admins can review and process applications
- AI assistant guides users through the process

## AI Assistant - Eliana D

### Configuration Options

| Setting | Description | Values |
|---------|-------------|--------|
| AI Name | Name of the assistant | Default: "Eliana D" |
| Tone | Communication style | professional, friendly, formal |
| Mode | Operational mode | sales, support, hybrid |
| Primary Color | Widget theme color | Hex color code |
| Welcome Message | Initial greeting | Custom text |
| Human Handoff | Allow escalation | Enabled/Disabled |

### Knowledge Base

Add content for the AI to reference:

```php
// Types available
- faq: Frequently asked questions
- document: Supporting documents
- policy: School policies
```

### Embed Widget

Generate embed code from AI Settings:

```html
<!-- Paste this in your school's website -->
<script>
(function() {
    window.ElianaD = {
        schoolId: '123',
        apiKey: 'ai_xxxxxxxxxxxx',
        config: {
            name: 'Eliana D',
            primaryColor: '#7C3AED',
            position: 'bottom-right'
        }
    };
    var d=document, s=d.createElement('script');
    s.src = 'https://yourdomain.com/js/eliana-widget.js';
    s.async = true;
    d.head.appendChild(s);
})();
</script>
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/ai/chat` | Send chat message |
| GET | `/ai/greeting` | Get welcome message |
| POST | `/ai/embed-chat` | Public embed chat |
| POST | `/ai/admission-flow` | AI-guided admission |
| POST | `/ai/clear` | Clear chat history |

## Multi-Tenant System

### User Roles

| Role | Description | Permissions |
|------|-------------|-------------|
| super_admin | System administrator | Full system access |
| admin | School administrator | Full school access |
| registrar | Admissions officer | Manage applications |
| accountant | Finance officer | View payments |
| reviewer | Application reviewer | Review applications |
| support | Support staff | Answer inquiries |
| student | Applicant | Own applications |

### Data Isolation

All tenant-specific data includes `school_id` foreign key:

```php
// Example: Query applications for current school
Application::where('school_id', auth()->user()->school_id)->get();
```

Middleware enforces isolation:

```php
// SchoolScopeMiddleware
- Filters queries by school_id
- Prevents cross-school data access

// RoleMiddleware
- Validates user roles
- Checks permissions
```

## Security

### Authentication
- Session-based with secure cookies
- Two-factor authentication (TOTP)
- Password hashing (bcrypt)
- Session timeout

### Authorization
- Role-based access control (RBAC)
- Permission middleware
- Feature-based access

### Protection
- CSRF tokens on all forms
- XSS output escaping
- SQL injection prevention (Eloquent)
- Rate limiting on API endpoints

### Audit Trail
- All admin actions logged
- Activity logs per user
- IP address tracking

## Queue Workers

```bash
# Start queue worker for all queues
php artisan queue:work redis

# Start worker for specific queues
php artisan queue:work redis --queue=emails,notifications

# Supervisor configuration
[program:admission-portal-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/admission-portal/artisan queue:work redis --queue=emails,notifications --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=4
```

## Default Login Credentials

### Super Admin
- **Email**: superadmin@admin.com
- **Password**: password

### Admin Users (after seeding)
- **Email**: admin@kmtc.ac.ke
- **Password**: password123

### Test Student
- **Email**: student@test.com
- **Password**: student123

## Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=Feature

# Run with coverage
php artisan test --coverage
```

## Production Deployment

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name admissions.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name admissions.yourdomain.com;
    
    root /var/www/admission-portal/public;
    index index.php;

    ssl_certificate /etc/ssl/certs/your-domain.crt;
    ssl_certificate_key /etc/ssl/private/your-domain.key;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Deployment Commands

```bash
# SSH into server
ssh user@your-server.com

# Navigate to project
cd /var/www/admission-portal

# Pull latest changes
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data /var/www/admission-portal

# Restart queue workers
sudo supervisorctl restart admission-portal-worker
```

## License

Proprietary - All rights reserved. See LICENSE file for details.

## Support

For support, email support@yourdomain.com
