# Royal Ambassadors OGBC Portal

A comprehensive web portal for managing the Royal Ambassadors program under Ogun State Baptist Convention (OGBC). This system provides user management, examination system, payment tracking, and administrative tools for the 25 associations under OGBC.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [System Requirements](#system-requirements)
- [Installation & Setup](#installation--setup)
- [Environment Configuration](#environment-configuration)
- [Database Setup](#database-setup)
- [Deployment to Namecheap VPS](#deployment-to-namecheap-vps)
- [Usage Guide](#usage-guide)
- [API Documentation](#api-documentation)
- [File Structure](#file-structure)
- [Security Features](#security-features)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

## Overview

The Royal Ambassadors OGBC Portal is a PHP-based web application built with a custom MVC framework. It serves the Royal Ambassadors program across 25 Baptist associations in Ogun State, Nigeria, providing tools for membership management, examination administration, payment processing, and communication.

### Key Statistics
- **25 Official Associations** managed
- **11 Hierarchical Ranks** from Candidate to Ambassador Plenipotentiary
- **3 User Roles**: Ambassador, Association President, Super Admin
- **Unique ID System**: OGBC/RA/XXXX format for all members

## Features

### 🔐 **Authentication & User Management**
- Secure registration and login system
- Role-based access control (Ambassador, President, Super Admin)
- Unique ID generation (OGBC/RA/XXXX format)
- Password hashing with bcrypt
- Profile management with avatar uploads

### 📚 **Examination System**
- Multiple-choice question creation and management
- Timed examination sessions
- Auto-grading with configurable pass marks
- Result approval workflow
- Progress tracking and certificates

### 💰 **Payment Management**
- Multiple payment types (dues, exam fees, camp registration)
- Receipt upload and verification
- Payment status tracking
- Financial reporting and analytics
- Super Admin approval system

### 📢 **Communication System**
- Role-based notification system
- Announcements and updates
- Association-specific messaging
- Email integration (configurable)

### 📝 **Content Management**
- Blog system for news and updates
- Image gallery for events and activities
- Document management
- Super Admin content control

### 🏕️ **Camp Registration**
- Excel file upload for participant lists
- Association-based registration management
- Participant tracking and reporting

### 📊 **Reporting & Analytics**
- User statistics and demographics
- Payment reports and financial summaries
- Examination performance analytics
- Association activity reports

## System Requirements

### Minimum Server Requirements
- **PHP**: 7.4 or higher (8.0+ recommended)
- **MySQL**: 5.7 or higher (8.0+ recommended)
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Memory**: 512MB RAM (1GB+ recommended)
- **Storage**: 2GB free space minimum
- **SSL Certificate**: Required for production

### PHP Extensions Required
```bash
- php-mysql (PDO MySQL driver)
- php-json
- php-mbstring
- php-curl
- php-gd (for image processing)
- php-fileinfo
- php-zip
```

## Installation & Setup

### 1. Download and Extract
```bash
# Download the project files
git clone <repository-url> ra-ogbc-portal
cd ra-ogbc-portal

# Or extract from zip file
unzip ra-ogbc-portal.zip
cd ra-ogbc-portal
```

### 2. Set File Permissions
```bash
# Make uploads directory writable
chmod -R 755 public/uploads/
chown -R www-data:www-data public/uploads/

# Make cache directory writable (if exists)
chmod -R 755 storage/cache/
chown -R www-data:www-data storage/cache/
```

### 3. Web Server Configuration

#### Apache Configuration
Create or update `.htaccess` in the root directory:
```apache
RewriteEngine On
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [L]

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# Prevent access to sensitive files
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "*.sql">
    Order allow,deny
    Deny from all
</Files>
```

#### Nginx Configuration
Add this server block to your Nginx configuration:
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /path/to/ra-ogbc-portal/public;
    
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\. {
        deny all;
    }
    
    location ~* \.(sql|env)$ {
        deny all;
    }
}
```

## Environment Configuration

### 1. Create Environment File
```bash
cp .env.example .env
```

### 2. Configure Database Settings
Edit the `.env` file:
```env
# Database Configuration
DB_HOST=localhost
DB_NAME=ra_ogbc_portal
DB_USER=your_db_user
DB_PASS=your_db_password

# Application Settings
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Security
SESSION_LIFETIME=7200
CSRF_TOKEN_LIFETIME=3600

# Email Configuration (Optional)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls

# File Upload Settings
MAX_UPLOAD_SIZE=10485760  # 10MB in bytes
ALLOWED_FILE_TYPES=jpg,jpeg,png,pdf,doc,docx,xlsx
```

## Database Setup

### 1. Create Database
```sql
CREATE DATABASE ra_ogbc_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ra_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON ra_ogbc_portal.* TO 'ra_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Run Database Migration

#### Option 1: Web-based Setup (Recommended)
1. Navigate to `https://your-domain.com/setup_database.php`
2. Click "Fresh Migration" to create all tables and seed data
3. Verify successful setup

#### Option 2: Command Line (if PHP CLI available)
```bash
php migrate.php fresh
```

### 3. Verify Installation
The migration creates:
- **25 Official Associations** (as per OGBC structure)
- **11 Rank Levels** (Candidate to Ambassador Plenipotentiary)
- **Sample Users** for testing
- **Sample Exams and Questions**
- **Payment and Notification Systems**

### 4. Default Login Accounts
```
Super Admin:
- Email: admin@ogbc.org
- Password: password123

President:
- Email: jane.smith@example.com
- Password: password123

Ambassador:
- Email: john.doe@example.com
- Password: password123
```

## Deployment to Namecheap VPS

### 1. VPS Preparation

#### Connect to Your VPS
```bash
ssh root@your-vps-ip
```

#### Update System
```bash
apt update && apt upgrade -y
```

#### Install LAMP Stack
```bash
# Install Apache
apt install apache2 -y
systemctl enable apache2
systemctl start apache2

# Install MySQL
apt install mysql-server -y
mysql_secure_installation

# Install PHP 8.0
apt install software-properties-common -y
add-apt-repository ppa:ondrej/php -y
apt update
apt install php8.0 php8.0-mysql php8.0-curl php8.0-json php8.0-mbstring php8.0-xml php8.0-zip php8.0-gd php8.0-fileinfo libapache2-mod-php8.0 -y

# Enable required Apache modules
a2enmod rewrite
a2enmod ssl
a2enmod headers
systemctl restart apache2
```

### 2. SSL Certificate Setup
```bash
# Install Certbot
apt install certbot python3-certbot-apache -y

# Get SSL Certificate
certbot --apache -d your-domain.com -d www.your-domain.com
```

### 3. Deploy Application

#### Upload Files
```bash
# Create web directory
mkdir -p /var/www/ra-ogbc-portal

# Upload files via SCP, SFTP, or Git
scp -r ./ra-ogbc-portal/* root@your-vps-ip:/var/www/ra-ogbc-portal/

# Or clone from repository
cd /var/www/
git clone <your-repository-url> ra-ogbc-portal
```

#### Set Permissions
```bash
chown -R www-data:www-data /var/www/ra-ogbc-portal
chmod -R 755 /var/www/ra-ogbc-portal
chmod -R 775 /var/www/ra-ogbc-portal/public/uploads
chmod -R 775 /var/www/ra-ogbc-portal/storage
```

### 4. Apache Virtual Host Configuration

#### Create Virtual Host File
```bash
nano /etc/apache2/sites-available/ra-ogbc-portal.conf
```

#### Add Configuration
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/ra-ogbc-portal
    
    <Directory /var/www/ra-ogbc-portal>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/ra-ogbc-error.log
    CustomLog ${APACHE_LOG_DIR}/ra-ogbc-access.log combined
    
    # Redirect to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>

<VirtualHost *:443>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/ra-ogbc-portal
    
    <Directory /var/www/ra-ogbc-portal>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/ra-ogbc-ssl-error.log
    CustomLog ${APACHE_LOG_DIR}/ra-ogbc-ssl-access.log combined
    
    # SSL Configuration (will be added by Certbot)
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/your-domain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/your-domain.com/privkey.pem
    Include /etc/letsencrypt/options-ssl-apache.conf
    SSLCertificateChainFile /etc/letsencrypt/live/your-domain.com/chain.pem
</VirtualHost>
```

#### Enable Site
```bash
a2ensite ra-ogbc-portal.conf
a2dissite 000-default.conf
systemctl reload apache2
```

### 5. Database Setup on VPS
```bash
# Login to MySQL
mysql -u root -p

# Create database and user
CREATE DATABASE ra_ogbc_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ra_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON ra_ogbc_portal.* TO 'ra_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 6. Configure Environment
```bash
cd /var/www/ra-ogbc-portal
cp .env.example .env
nano .env
```

Update with production values:
```env
DB_HOST=localhost
DB_NAME=ra_ogbc_portal
DB_USER=ra_user
DB_PASS=your_secure_password
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

### 7. Run Database Migration
Visit `https://your-domain.com/setup_database.php` and run "Fresh Migration"

### 8. Security Hardening

#### Firewall Configuration
```bash
ufw allow OpenSSH
ufw allow 'Apache Full'
ufw enable
```

#### Disable PHP Errors in Production
```bash
nano /etc/php/8.0/apache2/php.ini
```
Set: `display_errors = Off`

#### Regular Updates
```bash
# Create update script
nano /root/update_system.sh
```

```bash
#!/bin/bash
apt update && apt upgrade -y
certbot renew --quiet
systemctl restart apache2
```

```bash
chmod +x /root/update_system.sh
# Add to crontab for weekly updates
echo "0 2 * * 0 /root/update_system.sh" | crontab -
```

## Usage Guide

### For Ambassadors
1. **Registration**: Visit the registration page and fill in your details
2. **Dashboard**: View your profile, exam results, and payments
3. **Exams**: Take available examinations and track your progress
4. **Payments**: Submit payment receipts for verification
5. **Profile**: Update your information and upload avatar

### For Association Presidents
1. **Member Management**: View and manage members in your association
2. **Exam Oversight**: Monitor exam results and approvals
3. **Payment Verification**: Review and approve member payments
4. **Reports**: Generate association-specific reports

### For Super Admins
1. **System Management**: Full access to all system features
2. **User Administration**: Create, edit, and manage all users
3. **Content Management**: Manage blogs, gallery, and announcements
4. **Exam Administration**: Create and manage examinations
5. **Financial Oversight**: Monitor all payments and generate reports

## API Documentation

### Authentication Endpoints
```
POST /auth/login          - User login
POST /auth/register       - User registration
POST /auth/logout         - User logout
POST /auth/forgot-password - Password reset request
```

### User Management
```
GET  /api/users           - List users (Admin only)
GET  /api/users/{id}      - Get user details
PUT  /api/users/{id}      - Update user profile
DELETE /api/users/{id}    - Delete user (Admin only)
```

### Examination System
```
GET  /api/exams           - List available exams
GET  /api/exams/{id}      - Get exam details
POST /api/exams/{id}/submit - Submit exam answers
GET  /api/results         - Get user's exam results
```

### Payment System
```
GET  /api/payments        - List user payments
POST /api/payments        - Submit new payment
PUT  /api/payments/{id}   - Update payment status (Admin)
```

## File Structure

```
ra-ogbc-portal/
├── app/
│   ├── bootstrap.php           # Application bootstrap
│   ├── config/                 # Configuration files
│   │   ├── app.php
│   │   ├── database.php
│   │   └── roles.php
│   ├── controllers/            # MVC Controllers
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ExamController.php
│   │   ├── FinanceController.php
│   │   └── ...
│   ├── core/                   # Core framework files
│   │   ├── Controller.php
│   │   ├── DB.php
│   │   └── Router.php
│   ├── models/                 # Data models
│   │   ├── User.php
│   │   ├── Exam.php
│   │   └── Payment.php
│   ├── services/               # Business logic
│   │   └── DataService.php
│   └── views/                  # View templates
│       ├── layouts/
│       ├── ambassador/
│       ├── president/
│       └── admin/
├── database/                   # Database files
│   ├── migrations/
│   │   └── 001_create_core_tables.sql
│   └── seeds/
│       ├── 001_seed_associations_and_ranks.sql
│       └── 002_seed_sample_data.sql
├── public/                     # Public web root
│   ├── assets/                 # CSS, JS, images
│   ├── uploads/                # User uploads
│   └── index.php              # Entry point
├── .env                        # Environment configuration
├── .htaccess                   # Apache configuration
├── setup_database.php          # Database setup tool
└── README.md                   # This file
```

## Security Features

### Data Protection
- **Password Hashing**: bcrypt with salt
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Protection**: Input sanitization and output encoding
- **CSRF Protection**: Token-based form validation
- **File Upload Security**: Type and size validation

### Access Control
- **Role-based Permissions**: Ambassador, President, Super Admin levels
- **Session Security**: Secure session handling with timeouts
- **HTTPS Enforcement**: SSL/TLS encryption required
- **Input Validation**: Server-side validation for all forms

### Privacy Features
- **Data Encryption**: Sensitive data encrypted at rest
- **Audit Logging**: System activity tracking
- **Privacy Controls**: User consent and data management
- **Secure File Storage**: Protected upload directories

## Troubleshooting

### Common Issues

#### Database Connection Errors
```bash
# Check database service
sudo systemctl status mysql

# Verify credentials
mysql -u ra_user -p ra_ogbc_portal

# Check PHP MySQL extension
php -m | grep mysql
```

#### File Upload Issues
```bash
# Check permissions
ls -la public/uploads/
chmod -R 775 public/uploads/
chown -R www-data:www-data public/uploads/

# Check PHP settings
php -i | grep upload
```

#### Apache/Nginx Issues
```bash
# Check Apache status
sudo systemctl status apache2

# Check error logs
sudo tail -f /var/log/apache2/error.log

# Test configuration
sudo apache2ctl configtest
```

### Performance Optimization

#### Database Optimization
```sql
-- Add indexes for better performance
ALTER TABLE users ADD INDEX idx_email (email);
ALTER TABLE payments ADD INDEX idx_status (status);
ALTER TABLE exam_results ADD INDEX idx_user_exam (user_id, exam_id);
```

#### Caching Setup
```bash
# Install and configure Redis (optional)
apt install redis-server php-redis
systemctl enable redis-server
```

### Monitoring and Maintenance

#### Log Rotation
```bash
# Configure logrotate
nano /etc/logrotate.d/ra-ogbc-portal
```

```
/var/log/apache2/ra-ogbc*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 root root
    postrotate
        systemctl reload apache2
    endscript
}
```

#### Database Backups
```bash
# Create backup script
nano /root/backup_database.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u ra_user -p'your_password' ra_ogbc_portal > /root/backups/ra_ogbc_$DATE.sql
# Keep only last 30 backups
find /root/backups/ -name "ra_ogbc_*.sql" -type f -mtime +30 -delete
```

## Contributing

### Development Setup
1. Clone the repository
2. Set up local development environment (XAMPP, WAMP, or similar)
3. Import database schema
4. Configure `.env` file for development
5. Follow coding standards (PSR-4 autoloading, PSR-12 coding style)

### Coding Standards
- Follow PSR-4 autoloading standard
- Use meaningful variable and function names
- Comment complex logic thoroughly
- Validate all user inputs
- Use prepared statements for database queries

### Testing
- Test all user roles and permissions
- Verify file upload functionality
- Check responsive design on mobile devices
- Perform security testing
- Test database backup and restore procedures

## Support

For technical support or questions:
- **Documentation**: Refer to this README and inline code comments
- **Issues**: Check common troubleshooting section above
- **Updates**: Keep system updated with latest security patches

## License

This project is proprietary software developed for the Royal Ambassadors program under Ogun State Baptist Convention (OGBC). Unauthorized copying, distribution, or modification is prohibited.

---

**Royal Ambassadors OGBC Portal** - Empowering the next generation of Christian leaders through technology.

*"And he gave some, apostles; and some, prophets; and some, evangelists; and some, pastors and teachers; For the perfecting of the saints, for the work of the ministry, for the edifying of the body of Christ" - Ephesians 4:11-12*