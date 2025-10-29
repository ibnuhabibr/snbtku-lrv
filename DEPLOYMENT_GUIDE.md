# Panduan Deployment SNBTKU ke VPS

## Daftar Isi
1. [Quick Start (PHP 8.1.2 Ready)](#quick-start-php-812-ready)
2. [Persiapan VPS](#persiapan-vps)
3. [Instalasi Dependencies](#instalasi-dependencies)
4. [Setup Database](#setup-database)
5. [Deploy Aplikasi](#deploy-aplikasi)
6. [Konfigurasi Web Server](#konfigurasi-web-server)
7. [SSL Certificate](#ssl-certificate)
8. [Optimasi Performa](#optimasi-performa)
9. [Monitoring & Maintenance](#monitoring--maintenance)

## Quick Start (PHP 8.1.2 Ready)

Karena PHP 8.1.2 sudah terinstall di sistem Anda, berikut langkah cepat untuk melanjutkan deployment:

```bash
# 1. Pastikan PHP-FPM aktif
sudo systemctl enable php8.1-fpm
sudo systemctl start php8.1-fpm

# 2. Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# 3. Install Node.js (pilih salah satu metode di bawah)
# 4. Install MySQL dan Nginx
# 5. Lanjutkan ke section "Setup Database"
```

## Persiapan VPS

### Spesifikasi Minimum VPS
- **RAM**: 1GB (Recommended: 2GB)
- **Storage**: 20GB SSD
- **CPU**: 1 vCPU
- **OS**: Ubuntu 20.04 LTS atau Ubuntu 22.04 LTS

### Update System
```bash
sudo apt update && sudo apt upgrade -y
```

### Install Essential Packages
```bash
sudo apt install -y curl wget git unzip software-properties-common
```

## Instalasi Dependencies

### 1. Install PHP 8.1 (Recommended untuk Stabilitas)

**Metode 1 (RECOMMENDED): Menggunakan Default Ubuntu Repository**
```bash
# Hapus repository Sury yang bermasalah (jika ada)
sudo rm -f /etc/apt/sources.list.d/php.list

# Update dan install PHP 8.1 dari repository default Ubuntu 22.04
sudo apt update
sudo apt install -y php8.1 php8.1-fpm php8.1-mysql php8.1-xml php8.1-curl php8.1-zip php8.1-mbstring php8.1-gd php8.1-bcmath php8.1-intl php8.1-cli

# Verifikasi instalasi
php -v
```

**Metode 2 (Alternatif): Install PHP Generic**
```bash
# Jika versi spesifik tidak tersedia, gunakan package generic
sudo apt install -y php php-fpm php-mysql php-xml php-curl php-zip php-mbstring php-gd php-bcmath php-intl php-cli

# Cek versi yang terinstall
php -v
```

**Metode 3 (Alternatif untuk PHP 8.2): Menggunakan PPA Ondrej**
```bash
# Hanya jika Anda benar-benar membutuhkan PHP 8.2
# Hapus repository Sury yang bermasalah
sudo rm -f /etc/apt/sources.list.d/php.list

# Gunakan PPA Ondrej (lebih stabil)
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-zip php8.2-mbstring php8.2-gd php8.2-bcmath php8.2-intl php8.2-cli

# Kemudian ganti semua referensi php8.1 menjadi php8.2 di panduan ini
```

**CATATAN**: Panduan ini sudah disesuaikan untuk PHP 8.1 yang kompatible dengan Laravel 11.

### Verifikasi Instalasi PHP
```bash
# Cek versi PHP (harus menampilkan 8.1.x)
php -v

# Cek PHP-FPM service
sudo systemctl status php8.1-fpm

# Jika PHP-FPM belum aktif, aktifkan
sudo systemctl enable php8.1-fpm
sudo systemctl start php8.1-fpm

# Cek ekstensi PHP yang terinstall
php -m | grep -E "(mysql|curl|zip|mbstring|gd|bcmath|intl|xml)"
```

### 2. Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### 3. Install Node.js & NPM
```bash
# Metode 1 (Recommended): Menggunakan NodeSource repository
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Metode 2 (Jika Metode 1 gagal): Menggunakan Snap
sudo apt install -y snapd
sudo snap install node --classic

# Metode 3 (Alternatif): Menggunakan repository default Ubuntu
sudo apt install -y nodejs npm

# Verifikasi instalasi
node -v
npm -v
```

### 4. Install MySQL
```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

### 5. Install Nginx
```bash
sudo apt install -y nginx
```

## Setup Database

### 1. Login ke MySQL
```bash
sudo mysql -u root -p
```

### 2. Buat Database dan User
```sql
CREATE DATABASE snbtku_production;
CREATE USER 'snbtku_user'@'localhost' IDENTIFIED BY 'password_yang_kuat';
GRANT ALL PRIVILEGES ON snbtku_production.* TO 'snbtku_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Deploy Aplikasi

**CATATAN PENTING**: Panduan ini telah disesuaikan untuk PHP 8.1.2 yang sudah terinstall di sistem Anda. PHP 8.1.2 fully compatible dengan Laravel 11 dan Livewire 3.

### 1. Clone Repository
```bash
cd /var/www
sudo git clone https://github.com/username/snbtku.git
sudo chown -R www-data:www-data snbtku
cd snbtku
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
sudo -u www-data composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
sudo -u www-data npm install
```

### 3. Setup Environment
```bash
# Copy environment file
sudo -u www-data cp .env.example .env

# Edit environment file
sudo nano .env
```

### 4. Konfigurasi .env untuk Production
```env
APP_NAME="SNBTKU"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=snbtku_production
DB_USERNAME=snbtku_user
DB_PASSWORD=password_yang_kuat

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Generate Application Key
```bash
sudo -u www-data php artisan key:generate
```

### 6. Run Migrations dan Seeders
```bash
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan db:seed --force
```

### 7. Build Assets
```bash
sudo -u www-data npm run build
```

### 8. Setup Storage dan Cache
```bash
sudo -u www-data php artisan storage:link
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

### 9. Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/snbtku
sudo chmod -R 755 /var/www/snbtku
sudo chmod -R 775 /var/www/snbtku/storage
sudo chmod -R 775 /var/www/snbtku/bootstrap/cache
```

## Konfigurasi Web Server

### 1. Buat Nginx Virtual Host
```bash
sudo nano /etc/nginx/sites-available/snbtku
```

### 2. Konfigurasi Nginx
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/snbtku/public;

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
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;

    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 3. Enable Site
```bash
sudo ln -s /etc/nginx/sites-available/snbtku /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## SSL Certificate

### 1. Install Certbot
```bash
sudo apt install -y certbot python3-certbot-nginx
```

### 2. Generate SSL Certificate
```bash
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### 3. Auto-renewal Setup
```bash
sudo crontab -e
# Tambahkan baris berikut:
0 12 * * * /usr/bin/certbot renew --quiet
```

## Optimasi Performa

### 1. PHP-FPM Optimization
```bash
sudo nano /etc/php/8.1/fpm/pool.d/www.conf
```

Sesuaikan konfigurasi berikut:
```ini
pm = dynamic
pm.max_children = 20
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
pm.max_requests = 500
```

### 2. MySQL Optimization
```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Tambahkan konfigurasi:
```ini
[mysqld]
innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
query_cache_type = 1
query_cache_size = 32M
```

### 3. Restart Services
```bash
sudo systemctl restart php8.1-fpm
sudo systemctl restart mysql
sudo systemctl restart nginx
```

## Monitoring & Maintenance

### 1. Setup Log Rotation
```bash
sudo nano /etc/logrotate.d/snbtku
```

```
/var/www/snbtku/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
}
```

### 2. Backup Script
```bash
sudo nano /usr/local/bin/snbtku-backup.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/snbtku"
APP_DIR="/var/www/snbtku"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u snbtku_user -p snbtku_production > $BACKUP_DIR/database_$DATE.sql

# Backup application files
tar -czf $BACKUP_DIR/app_$DATE.tar.gz -C /var/www snbtku

# Remove old backups (keep last 7 days)
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

```bash
sudo chmod +x /usr/local/bin/snbtku-backup.sh
```

### 3. Setup Cron untuk Backup
```bash
sudo crontab -e
# Tambahkan:
0 2 * * * /usr/local/bin/snbtku-backup.sh
```

### 4. Monitoring Commands
```bash
# Check application status
sudo systemctl status nginx php8.1-fpm mysql

# Check disk usage
df -h

# Check memory usage
free -h

# Check logs
sudo tail -f /var/www/snbtku/storage/logs/laravel.log
sudo tail -f /var/log/nginx/error.log
```

## Update Deployment

### Script Update Otomatis
```bash
sudo nano /usr/local/bin/snbtku-update.sh
```

```bash
#!/bin/bash
cd /var/www/snbtku

# Backup current version
sudo -u www-data cp .env .env.backup

# Pull latest changes
sudo -u www-data git pull origin main

# Install/update dependencies
sudo -u www-data composer install --optimize-autoloader --no-dev
sudo -u www-data npm install
sudo -u www-data npm run build

# Run migrations
sudo -u www-data php artisan migrate --force

# Clear and cache
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# Restart services
sudo systemctl reload php8.1-fpm
sudo systemctl reload nginx

echo "Update completed successfully!"
```

```bash
sudo chmod +x /usr/local/bin/snbtku-update.sh
```

## Troubleshooting

### Installation Issues

1. **Repository Sury Error "418 I'm a teapot"**
```bash
# Error: 418 I'm a teapot [IP: 151.101.195.52 443]
# Ini menunjukkan server Sury sedang bermasalah atau memblokir akses

# SOLUSI LANGSUNG: Hapus repository bermasalah dan gunakan default Ubuntu
sudo rm -f /etc/apt/sources.list.d/php.list
sudo apt update
sudo apt install -y php8.1 php8.1-fpm php8.1-mysql php8.1-xml php8.1-curl php8.1-zip php8.1-mbstring php8.1-gd php8.1-bcmath php8.1-intl php8.1-cli

# Verifikasi instalasi berhasil
php -v
systemctl status php8.1-fpm
```

2. **PPA Connection Timeout**
```bash
# Error: TimeoutError: [Errno 110] Connection timed out
# Solusi 1: Gunakan repository default Ubuntu (lihat di atas)

# Solusi 2: Cek koneksi internet
ping -c 4 8.8.8.8
ping -c 4 packages.sury.org

# Solusi 3: Restart networking
sudo systemctl restart networking
sudo systemctl restart systemd-resolved

# Solusi 4: Flush DNS
sudo systemd-resolve --flush-caches
```

2. **Firewall/Network Issues**
```bash
# Cek status firewall
sudo ufw status

# Jika firewall aktif, pastikan port HTTP/HTTPS terbuka
sudo ufw allow 80
sudo ufw allow 443

# Cek DNS resolution
nslookup packages.sury.org
nslookup ppa.launchpad.net
```

### Common Issues

1. **Permission Issues**
```bash
sudo chown -R www-data:www-data /var/www/snbtku
sudo chmod -R 755 /var/www/snbtku
sudo chmod -R 775 /var/www/snbtku/storage
sudo chmod -R 775 /var/www/snbtku/bootstrap/cache
```

2. **Database Connection Issues**
```bash
# Check MySQL status
sudo systemctl status mysql

# Test database connection
mysql -u snbtku_user -p snbtku_production
```

3. **PHP-FPM Issues**
```bash
# Check PHP-FPM status
sudo systemctl status php8.1-fpm

# Check PHP-FPM logs
sudo tail -f /var/log/php8.1-fpm.log
```

4. **Nginx Issues**
```bash
# Test nginx configuration
sudo nginx -t

# Check nginx logs
sudo tail -f /var/log/nginx/error.log
```

## Security Checklist

- [ ] Firewall dikonfigurasi (UFW)
- [ ] SSH key-based authentication
- [ ] Database user dengan privilege minimal
- [ ] SSL certificate terpasang
- [ ] Regular security updates
- [ ] Backup otomatis berjalan
- [ ] Log monitoring aktif
- [ ] File permissions benar

## Kontak Support

Jika mengalami masalah deployment, silakan buat issue di repository atau hubungi tim development.

---

**Catatan**: Ganti `yourdomain.com` dengan domain aktual Anda dan sesuaikan password database dengan yang lebih kuat.