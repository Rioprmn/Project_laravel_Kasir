# 🚀 Panduan Migrasi Laravel Kasir POS ke Ubuntu Server Lokal

## 📋 Spesifikasi Sistem

| Komponen   | Versi/Teknologi               |
| ---------- | ----------------------------- |
| OS Server  | Ubuntu Server 24.04.2 LTS     |
| Framework  | Laravel 11                    |
| PHP        | 8.3                           |
| Web Server | Apache2                       |
| Database   | MySQL 8.0                     |
| DNS/SSL    | Cloudflare Tunnel             |
| Frontend   | Vite + TailwindCSS + AlpineJS |

---

## 📦 BAGIAN 1: Instalasi Runtime di Ubuntu Server

### 1.1 Update Sistem

```bash
sudo apt update && sudo apt upgrade -y
```

### 1.2 Instalasi PHP 8.3 dan Ekstensi yang Diperlukan

```bash
# Tambah repository PHP
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.3 dan ekstensi yang dibutuhkan Laravel 11
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-common \
    php8.3-mysql php8.3-zip php8.3-gd php8.3-mbstring php8.3-curl \
    php8.3-xml php8.3-bcmath php8.3-intl php8.3-readline \
    php8.3-tokenizer php8.3-fileinfo php8.3-ctype php8.3-json \
    php8.3-opcache php8.3-sqlite3

# Verifikasi instalasi
php -v
```

### 1.3 Instalasi Apache2

```bash
# Install Apache2
sudo apt install -y apache2 libapache2-mod-php8.3

# Enable modul yang diperlukan
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod ssl
sudo a2enmod php8.3
sudo a2enmod proxy_fcgi setenvif

# Restart Apache
sudo systemctl restart apache2
sudo systemctl enable apache2
```

### 1.4 Instalasi MySQL 8.0

```bash
# Install MySQL Server
sudo apt install -y mysql-server mysql-client

# Amankan instalasi MySQL
sudo mysql_secure_installation

# Masuk ke MySQL dan buat database
sudo mysql -u root -p
```

```sql
-- Di dalam MySQL shell
CREATE DATABASE kasir_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'kasir_user'@'localhost' IDENTIFIED BY 'password_kuat_anda';
GRANT ALL PRIVILEGES ON kasir_pos.* TO 'kasir_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 1.5 Instalasi Composer

```bash
# Download dan install Composer
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

# Verifikasi
composer --version
```

### 1.6 Instalasi Node.js dan NPM (untuk Vite)

```bash
# Install Node.js 20 LTS (direkomendasikan untuk Vite 6)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Verifikasi
node -v
npm -v
```

### 1.7 Instalasi Git

```bash
sudo apt install -y git
```

### 1.8 Dependensi Sistem Tambahan

Berdasarkan audit `composer.json` dan `package.json` Anda:

```bash
# Dependensi untuk image processing (GD)
sudo apt install -y libpng-dev libjpeg-dev libfreetype6-dev

# Dependensi untuk zip
sudo apt install -y zip unzip

# Dependensi untuk intl
sudo apt install -y libicu-dev

# Dependensi untuk curl
sudo apt install -y libcurl4-openssl-dev

# Tools tambahan
sudo apt install -y wget curl vim htop
```

---

## 📤 BAGIAN 2: Ekspor Database dari Railway

### 2.1 Ekspor Database dari Railway

**Metode 1: Via Railway CLI**

```bash
# Install Railway CLI (di komputer lokal dengan akses internet)
npm install -g @railway/cli

# Login ke Railway
railway login

# Link ke project
railway link

# Ekspor database
railway run mysqldump -u root -p'usDqYBMjGAiwWZZOsSSrhynRsSSXLUPR' \
    -h mysql.railway.internal -P 3306 railway > railway_backup.sql
```

**Metode 2: Via MySQL Client (Gunakan Public URL Railway)**

Di Railway Dashboard:

1. Buka MySQL service
2. Klik tab **Connect**
3. Copy **Public URL** atau credentials
4. Jalankan dari terminal:

```bash
# Ganti dengan credentials dari Railway Dashboard (Public URL)
mysqldump -h <RAILWAY_PUBLIC_HOST> -P <PORT> -u root -p'<PASSWORD>' railway > railway_backup.sql
```

**Metode 3: Via Railway Data Transfer (Recommended)**

1. Buka Railway Dashboard → MySQL Service
2. Klik **Data** tab
3. Klik **Export** untuk download SQL dump

### 2.2 Import Database ke MySQL Lokal

```bash
# Transfer file SQL ke Ubuntu Server (via SCP, USB, dll)
# Contoh via SCP dari komputer lokal:
scp railway_backup.sql user@server-ip:/home/user/

# Di Ubuntu Server - Import database
mysql -u kasir_user -p kasir_pos < /home/user/railway_backup.sql

# Verifikasi import
mysql -u kasir_user -p -e "USE kasir_pos; SHOW TABLES;"
```

### 2.3 Migrasi Data yang Aman

```bash
# Jalankan migrasi Laravel untuk memastikan schema up-to-date
cd /var/www/kasir-pos
php artisan migrate --force

# Clear cache setelah migrasi
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## 🌐 BAGIAN 3: Konfigurasi Apache VirtualHost

### 3.1 Buat File VirtualHost

```bash
sudo nano /etc/apache2/sites-available/kasir-pos.conf
```

Isi dengan konfigurasi berikut:

```apache
<VirtualHost *:80>
    # ================================
    # KASIR POS - Laravel Application
    # ================================

    ServerName kasir.yourdomain.com
    ServerAlias www.kasir.yourdomain.com
    ServerAdmin admin@yourdomain.com

    # Document Root - Pointing ke folder public Laravel
    DocumentRoot /var/www/kasir-pos/public

    # Directory Configuration
    <Directory /var/www/kasir-pos/public>
        Options -Indexes +FollowSymLinks +MultiViews
        AllowOverride All
        Require all granted

        # Enable .htaccess
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^ index.php [L]
        </IfModule>
    </Directory>

    # Prevent access to sensitive files
    <FilesMatch "^\.">
        Require all denied
    </FilesMatch>

    <DirectoryMatch "/\.git">
        Require all denied
    </DirectoryMatch>

    # PHP Configuration
    <FilesMatch \.php$>
        SetHandler application/x-httpd-php
    </FilesMatch>

    # Security Headers
    <IfModule mod_headers.c>
        Header always set X-Content-Type-Options "nosniff"
        Header always set X-Frame-Options "SAMEORIGIN"
        Header always set X-XSS-Protection "1; mode=block"
        Header always set Referrer-Policy "strict-origin-when-cross-origin"
    </IfModule>

    # Logging
    ErrorLog ${APACHE_LOG_DIR}/kasir-pos-error.log
    CustomLog ${APACHE_LOG_DIR}/kasir-pos-access.log combined

    # PHP Values (Optional - dapat diatur di php.ini)
    php_value upload_max_filesize 64M
    php_value post_max_size 64M
    php_value max_execution_time 300
    php_value memory_limit 256M

</VirtualHost>

# ================================
# HTTPS Configuration (untuk Cloudflare)
# ================================
<VirtualHost *:443>
    ServerName kasir.yourdomain.com
    ServerAlias www.kasir.yourdomain.com
    ServerAdmin admin@yourdomain.com

    DocumentRoot /var/www/kasir-pos/public

    <Directory /var/www/kasir-pos/public>
        Options -Indexes +FollowSymLinks +MultiViews
        AllowOverride All
        Require all granted
    </Directory>

    # Cloudflare Origin Certificate (opsional jika pakai Tunnel)
    # SSLEngine on
    # SSLCertificateFile /etc/ssl/cloudflare/kasir-pos.pem
    # SSLCertificateKeyFile /etc/ssl/cloudflare/kasir-pos.key

    # Logging
    ErrorLog ${APACHE_LOG_DIR}/kasir-pos-ssl-error.log
    CustomLog ${APACHE_LOG_DIR}/kasir-pos-ssl-access.log combined

</VirtualHost>
```

### 3.2 Aktifkan Site dan Restart Apache

```bash
# Disable default site
sudo a2dissite 000-default.conf

# Enable kasir-pos site
sudo a2ensite kasir-pos.conf

# Test konfigurasi Apache
sudo apache2ctl configtest

# Restart Apache
sudo systemctl restart apache2
```

### 3.3 Set Permission Laravel

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/kasir-pos

# Set directory permissions
sudo find /var/www/kasir-pos -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/kasir-pos -type f -exec chmod 644 {} \;

# Special permissions untuk storage dan bootstrap/cache
sudo chmod -R 775 /var/www/kasir-pos/storage
sudo chmod -R 775 /var/www/kasir-pos/bootstrap/cache

# Pastikan www-data bisa menulis
sudo chgrp -R www-data /var/www/kasir-pos/storage /var/www/kasir-pos/bootstrap/cache
```

---

## ☁️ BAGIAN 4: Setup Cloudflare Tunnel (Zero Trust)

### 4.1 Daftar dan Setup Cloudflare

1. Buat akun di [Cloudflare](https://cloudflare.com)
2. Tambahkan domain Anda
3. Update nameserver domain ke Cloudflare

### 4.2 Install Cloudflared di Ubuntu Server

```bash
# Download dan install cloudflared
curl -L --output cloudflared.deb https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb
sudo dpkg -i cloudflared.deb
rm cloudflared.deb

# Verifikasi instalasi
cloudflared --version
```

### 4.3 Autentikasi Cloudflared

```bash
# Login ke Cloudflare (akan membuka browser untuk autentikasi)
cloudflared tunnel login
```

### 4.4 Buat Tunnel

```bash
# Buat tunnel baru
cloudflared tunnel create kasir-pos

# Output akan menampilkan Tunnel ID, simpan ini!
# Contoh: Created tunnel kasir-pos with id abc123-def456-...
```

### 4.5 Konfigurasi Tunnel

```bash
# Buat folder konfigurasi
mkdir -p ~/.cloudflared

# Buat file konfigurasi
nano ~/.cloudflared/config.yml
```

Isi dengan:

```yaml
# Cloudflare Tunnel Configuration
tunnel: <TUNNEL_ID_ANDA>
credentials-file: /root/.cloudflared/<TUNNEL_ID_ANDA>.json

ingress:
    # Kasir POS Application
    - hostname: kasir.yourdomain.com
      service: http://localhost:80
      originRequest:
          noTLSVerify: true

    # Catch-all rule (required)
    - service: http_status:404
```

### 4.6 Route DNS ke Tunnel

```bash
# Tambahkan DNS record untuk tunnel
cloudflared tunnel route dns kasir-pos kasir.yourdomain.com
```

### 4.7 Jalankan Tunnel sebagai Service

```bash
# Install tunnel sebagai system service
sudo cloudflared service install

# Start service
sudo systemctl start cloudflared
sudo systemctl enable cloudflared

# Cek status
sudo systemctl status cloudflared
```

### 4.8 Konfigurasi SSL di Cloudflare Dashboard

1. Buka Cloudflare Dashboard → Domain Anda
2. Pergi ke **SSL/TLS** → **Overview**
3. Pilih mode **Full** atau **Full (Strict)**
4. Di **Edge Certificates**, aktifkan:
    - Always Use HTTPS
    - Automatic HTTPS Rewrites

---

## 🔍 BAGIAN 5: Audit Dependensi

### 5.1 Audit composer.json

| Package                     | Kebutuhan Sistem                                                                               |
| --------------------------- | ---------------------------------------------------------------------------------------------- |
| `php: ^8.2`                 | ✅ PHP 8.3 sudah terinstall                                                                    |
| `laravel/framework: ^11.31` | ✅ Membutuhkan ekstensi: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML |
| `laravel/tinker`            | ✅ Membutuhkan ekstensi: readline                                                              |
| `laravel/breeze`            | ✅ Untuk auth scaffolding                                                                      |
| `phpunit/phpunit`           | ✅ Untuk testing                                                                               |

**Ekstensi PHP yang WAJIB diinstall:**

```bash
sudo apt install -y php8.3-bcmath php8.3-ctype php8.3-fileinfo \
    php8.3-mbstring php8.3-pdo php8.3-tokenizer php8.3-xml \
    php8.3-mysql php8.3-readline php8.3-curl php8.3-zip php8.3-gd
```

### 5.2 Audit package.json

| Package                 | Kebutuhan Sistem                   |
| ----------------------- | ---------------------------------- |
| `vite: ^6.0.11`         | ✅ Node.js 18+ (disarankan 20 LTS) |
| `tailwindcss: ^3.1.0`   | ✅ Node.js & npm                   |
| `alpinejs: ^3.4.2`      | ✅ Bundled via Vite                |
| `autoprefixer: ^10.4.2` | ✅ PostCSS dependency              |
| `postcss: ^8.4.31`      | ✅ CSS processing                  |
| `axios: ^1.7.4`         | ✅ HTTP client                     |
| `concurrently: ^9.0.1`  | ✅ Dev tool untuk parallel scripts |

**Node.js yang dibutuhkan:** v20.x LTS

---

## 🤖 BAGIAN 6: Skrip Autodeploy

Buat file `deploy.sh` di root project:

```bash
#!/bin/bash

#====================================================================
# KASIR POS - AUTODEPLOY SCRIPT
# Laravel 11 | PHP 8.3 | Apache2 | MySQL | Cloudflare Tunnel
# Ubuntu Server 24.04.2 LTS
#====================================================================

set -e  # Exit on error

# ==================== KONFIGURASI ====================
APP_NAME="kasir-pos"
APP_DIR="/var/www/${APP_NAME}"
REPO_URL="https://github.com/yourusername/kasir-pos.git"  # Ganti dengan repo Anda
BRANCH="main"
DB_NAME="kasir_pos"
DB_USER="kasir_user"
DB_PASS="password_kuat_anda"  # GANTI!
DOMAIN="kasir.yourdomain.com"
CLOUDFLARE_TUNNEL_NAME="kasir-pos"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# ==================== FUNCTIONS ====================

print_header() {
    echo ""
    echo -e "${BLUE}=====================================================================${NC}"
    echo -e "${BLUE}  $1${NC}"
    echo -e "${BLUE}=====================================================================${NC}"
    echo ""
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

check_root() {
    if [[ $EUID -ne 0 ]]; then
        print_error "Script ini harus dijalankan sebagai root!"
        echo "Gunakan: sudo bash deploy.sh"
        exit 1
    fi
}

# ==================== STEP 1: SYSTEM UPDATE ====================
install_system_packages() {
    print_header "STEP 1: Update Sistem & Install Packages"

    apt update && apt upgrade -y

    # Install dependencies
    apt install -y software-properties-common curl wget git zip unzip \
        libpng-dev libjpeg-dev libfreetype6-dev libicu-dev libcurl4-openssl-dev

    print_success "System packages installed"
}

# ==================== STEP 2: PHP 8.3 ====================
install_php() {
    print_header "STEP 2: Install PHP 8.3"

    # Add PHP repository
    add-apt-repository ppa:ondrej/php -y
    apt update

    # Install PHP and extensions
    apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-common \
        php8.3-mysql php8.3-zip php8.3-gd php8.3-mbstring php8.3-curl \
        php8.3-xml php8.3-bcmath php8.3-intl php8.3-readline \
        php8.3-tokenizer php8.3-fileinfo php8.3-ctype \
        php8.3-opcache php8.3-sqlite3

    # Configure PHP
    sed -i 's/upload_max_filesize = .*/upload_max_filesize = 64M/' /etc/php/8.3/apache2/php.ini
    sed -i 's/post_max_size = .*/post_max_size = 64M/' /etc/php/8.3/apache2/php.ini
    sed -i 's/memory_limit = .*/memory_limit = 256M/' /etc/php/8.3/apache2/php.ini
    sed -i 's/max_execution_time = .*/max_execution_time = 300/' /etc/php/8.3/apache2/php.ini

    print_success "PHP 8.3 installed: $(php -v | head -n 1)"
}

# ==================== STEP 3: APACHE2 ====================
install_apache() {
    print_header "STEP 3: Install & Configure Apache2"

    apt install -y apache2 libapache2-mod-php8.3

    # Enable modules
    a2enmod rewrite headers ssl php8.3 proxy_fcgi setenvif

    # Create VirtualHost
    cat > /etc/apache2/sites-available/${APP_NAME}.conf <<EOF
<VirtualHost *:80>
    ServerName ${DOMAIN}
    ServerAlias www.${DOMAIN}
    ServerAdmin admin@${DOMAIN}

    DocumentRoot ${APP_DIR}/public

    <Directory ${APP_DIR}/public>
        Options -Indexes +FollowSymLinks +MultiViews
        AllowOverride All
        Require all granted
    </Directory>

    <FilesMatch "^\.">
        Require all denied
    </FilesMatch>

    <DirectoryMatch "/\.git">
        Require all denied
    </DirectoryMatch>

    <IfModule mod_headers.c>
        Header always set X-Content-Type-Options "nosniff"
        Header always set X-Frame-Options "SAMEORIGIN"
        Header always set X-XSS-Protection "1; mode=block"
    </IfModule>

    ErrorLog \${APACHE_LOG_DIR}/${APP_NAME}-error.log
    CustomLog \${APACHE_LOG_DIR}/${APP_NAME}-access.log combined

    php_value upload_max_filesize 64M
    php_value post_max_size 64M
    php_value max_execution_time 300
    php_value memory_limit 256M
</VirtualHost>
EOF

    # Enable site
    a2dissite 000-default.conf 2>/dev/null || true
    a2ensite ${APP_NAME}.conf

    systemctl restart apache2
    systemctl enable apache2

    print_success "Apache2 configured"
}

# ==================== STEP 4: MYSQL ====================
install_mysql() {
    print_header "STEP 4: Install & Configure MySQL"

    apt install -y mysql-server mysql-client

    # Start MySQL
    systemctl start mysql
    systemctl enable mysql

    # Create database and user
    mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
    mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
    mysql -e "FLUSH PRIVILEGES;"

    print_success "MySQL configured - Database: ${DB_NAME}"
}

# ==================== STEP 5: COMPOSER ====================
install_composer() {
    print_header "STEP 5: Install Composer"

    if ! command -v composer &> /dev/null; then
        curl -sS https://getcomposer.org/installer | php
        mv composer.phar /usr/local/bin/composer
        chmod +x /usr/local/bin/composer
    fi

    print_success "Composer installed: $(composer --version)"
}

# ==================== STEP 6: NODE.JS ====================
install_nodejs() {
    print_header "STEP 6: Install Node.js 20 LTS"

    if ! command -v node &> /dev/null; then
        curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
        apt install -y nodejs
    fi

    print_success "Node.js installed: $(node -v)"
    print_success "NPM installed: $(npm -v)"
}

# ==================== STEP 7: DEPLOY APPLICATION ====================
deploy_application() {
    print_header "STEP 7: Deploy Laravel Application"

    # Create directory
    mkdir -p ${APP_DIR}

    # Clone or pull repository
    if [ -d "${APP_DIR}/.git" ]; then
        print_warning "Repository exists, pulling latest changes..."
        cd ${APP_DIR}
        git fetch --all
        git reset --hard origin/${BRANCH}
    else
        print_warning "Cloning repository..."
        git clone -b ${BRANCH} ${REPO_URL} ${APP_DIR}
        cd ${APP_DIR}
    fi

    # Install PHP dependencies
    print_warning "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction

    # Install Node dependencies and build assets
    print_warning "Installing NPM dependencies and building assets..."
    npm ci
    npm run build

    # Create .env file
    if [ ! -f "${APP_DIR}/.env" ]; then
        cp ${APP_DIR}/.env.example ${APP_DIR}/.env
    fi

    # Configure .env
    sed -i "s|APP_ENV=.*|APP_ENV=production|" ${APP_DIR}/.env
    sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" ${APP_DIR}/.env
    sed -i "s|APP_URL=.*|APP_URL=https://${DOMAIN}|" ${APP_DIR}/.env
    sed -i "s|DB_CONNECTION=.*|DB_CONNECTION=mysql|" ${APP_DIR}/.env
    sed -i "s|DB_HOST=.*|DB_HOST=127.0.0.1|" ${APP_DIR}/.env
    sed -i "s|DB_PORT=.*|DB_PORT=3306|" ${APP_DIR}/.env
    sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|" ${APP_DIR}/.env
    sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USER}|" ${APP_DIR}/.env
    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" ${APP_DIR}/.env

    # Generate app key if needed
    php artisan key:generate --force

    # Run migrations
    php artisan migrate --force

    # Optimize Laravel
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan storage:link

    print_success "Laravel application deployed"
}

# ==================== STEP 8: SET PERMISSIONS ====================
set_permissions() {
    print_header "STEP 8: Set File Permissions"

    chown -R www-data:www-data ${APP_DIR}
    find ${APP_DIR} -type d -exec chmod 755 {} \;
    find ${APP_DIR} -type f -exec chmod 644 {} \;
    chmod -R 775 ${APP_DIR}/storage
    chmod -R 775 ${APP_DIR}/bootstrap/cache

    print_success "Permissions set"
}

# ==================== STEP 9: CLOUDFLARE TUNNEL ====================
setup_cloudflare_tunnel() {
    print_header "STEP 9: Setup Cloudflare Tunnel"

    # Install cloudflared
    if ! command -v cloudflared &> /dev/null; then
        curl -L --output cloudflared.deb https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb
        dpkg -i cloudflared.deb
        rm cloudflared.deb
    fi

    print_success "Cloudflared installed: $(cloudflared --version)"

    echo ""
    print_warning "PENTING: Untuk setup Cloudflare Tunnel, jalankan manual:"
    echo "  1. cloudflared tunnel login"
    echo "  2. cloudflared tunnel create ${CLOUDFLARE_TUNNEL_NAME}"
    echo "  3. Buat file ~/.cloudflared/config.yml"
    echo "  4. cloudflared tunnel route dns ${CLOUDFLARE_TUNNEL_NAME} ${DOMAIN}"
    echo "  5. sudo cloudflared service install"
    echo ""
}

# ==================== STEP 10: SETUP CRON ====================
setup_cron() {
    print_header "STEP 10: Setup Laravel Scheduler (Cron)"

    # Add Laravel scheduler to crontab
    CRON_JOB="* * * * * cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1"

    (crontab -u www-data -l 2>/dev/null | grep -v "artisan schedule:run"; echo "${CRON_JOB}") | crontab -u www-data -

    print_success "Laravel scheduler cron added"
}

# ==================== STEP 11: SETUP QUEUE WORKER ====================
setup_queue_worker() {
    print_header "STEP 11: Setup Queue Worker (Supervisor)"

    apt install -y supervisor

    cat > /etc/supervisor/conf.d/${APP_NAME}-worker.conf <<EOF
[program:${APP_NAME}-worker]
process_name=%(program_name)s_%(process_num)02d
command=php ${APP_DIR}/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=${APP_DIR}/storage/logs/worker.log
stopwaitsecs=3600
EOF

    supervisorctl reread
    supervisorctl update
    supervisorctl start ${APP_NAME}-worker:*

    print_success "Queue worker configured"
}

# ==================== STEP 12: FIREWALL ====================
setup_firewall() {
    print_header "STEP 12: Setup Firewall (UFW)"

    ufw --force enable
    ufw default deny incoming
    ufw default allow outgoing
    ufw allow ssh
    ufw allow 80/tcp
    ufw allow 443/tcp

    print_success "Firewall configured"
}

# ==================== MAIN EXECUTION ====================
main() {
    clear
    echo ""
    echo -e "${GREEN}"
    echo "  ██╗  ██╗ █████╗ ███████╗██╗██████╗     ██████╗  ██████╗ ███████╗"
    echo "  ██║ ██╔╝██╔══██╗██╔════╝██║██╔══██╗    ██╔══██╗██╔═══██╗██╔════╝"
    echo "  █████╔╝ ███████║███████╗██║██████╔╝    ██████╔╝██║   ██║███████╗"
    echo "  ██╔═██╗ ██╔══██║╚════██║██║██╔══██╗    ██╔═══╝ ██║   ██║╚════██║"
    echo "  ██║  ██╗██║  ██║███████║██║██║  ██║    ██║     ╚██████╔╝███████║"
    echo "  ╚═╝  ╚═╝╚═╝  ╚═╝╚══════╝╚═╝╚═╝  ╚═╝    ╚═╝      ╚═════╝ ╚══════╝"
    echo -e "${NC}"
    echo "  AUTODEPLOY SCRIPT - Ubuntu Server 24.04"
    echo "  Laravel 11 | PHP 8.3 | Apache2 | MySQL | Cloudflare Tunnel"
    echo ""

    check_root

    echo -e "${YELLOW}Pilih mode instalasi:${NC}"
    echo "  1) Full Installation (Fresh Server)"
    echo "  2) Update Application Only"
    echo "  3) Install Runtime Only (PHP, Apache, MySQL, Node)"
    echo "  4) Deploy Application Only"
    echo "  5) Setup Cloudflare Tunnel Only"
    echo "  6) Exit"
    echo ""
    read -p "Pilihan [1-6]: " choice

    case $choice in
        1)
            install_system_packages
            install_php
            install_apache
            install_mysql
            install_composer
            install_nodejs
            deploy_application
            set_permissions
            setup_cloudflare_tunnel
            setup_cron
            setup_queue_worker
            setup_firewall
            ;;
        2)
            deploy_application
            set_permissions
            systemctl restart apache2
            supervisorctl restart ${APP_NAME}-worker:* 2>/dev/null || true
            ;;
        3)
            install_system_packages
            install_php
            install_apache
            install_mysql
            install_composer
            install_nodejs
            ;;
        4)
            deploy_application
            set_permissions
            ;;
        5)
            setup_cloudflare_tunnel
            ;;
        6)
            echo "Bye!"
            exit 0
            ;;
        *)
            print_error "Pilihan tidak valid"
            exit 1
            ;;
    esac

    print_header "DEPLOYMENT COMPLETE!"

    echo -e "${GREEN}Summary:${NC}"
    echo "  • Application: ${APP_DIR}"
    echo "  • Domain: https://${DOMAIN}"
    echo "  • Database: ${DB_NAME}"
    echo "  • Logs: ${APP_DIR}/storage/logs/"
    echo ""
    echo -e "${YELLOW}Next Steps:${NC}"
    echo "  1. Import database dari Railway (jika belum)"
    echo "  2. Configure Cloudflare Tunnel"
    echo "  3. Update .env sesuai kebutuhan"
    echo "  4. Test aplikasi: https://${DOMAIN}"
    echo ""
}

# Run main function
main "$@"
```

---

## 📝 BAGIAN 7: Quick Commands Reference

### Deploy Cepat

```bash
# Download dan jalankan script
wget https://raw.githubusercontent.com/yourusername/kasir-pos/main/deploy.sh
chmod +x deploy.sh
sudo ./deploy.sh
```

### Maintenance Commands

```bash
# Clear all cache
cd /var/www/kasir-pos
php artisan optimize:clear

# Update application
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan optimize

# View logs
tail -f /var/www/kasir-pos/storage/logs/laravel.log

# Restart services
sudo systemctl restart apache2
sudo supervisorctl restart kasir-pos-worker:*
sudo systemctl restart cloudflared
```

### Debug Commands

```bash
# Check Apache status
sudo systemctl status apache2

# Check Cloudflare Tunnel status
sudo systemctl status cloudflared

# Check PHP version
php -v

# Check Laravel
cd /var/www/kasir-pos && php artisan --version

# Check database connection
php artisan db:show

# Test MySQL connection
mysql -u kasir_user -p -e "SELECT 1"
```

---

## ⚠️ Checklist Sebelum Go-Live

-   [ ] Backup database Railway sudah diexport
-   [ ] Database sudah diimport ke MySQL lokal
-   [ ] File `.env` sudah dikonfigurasi untuk production
-   [ ] `APP_DEBUG=false`
-   [ ] `APP_ENV=production`
-   [ ] SSL/HTTPS aktif via Cloudflare
-   [ ] Permission folder storage dan bootstrap/cache sudah benar
-   [ ] Cron job untuk Laravel scheduler sudah aktif
-   [ ] Queue worker sudah berjalan
-   [ ] Firewall sudah dikonfigurasi
-   [ ] Cloudflare Tunnel sudah berjalan dan terhubung
-   [ ] Testing semua fitur aplikasi

---

## 🆘 Troubleshooting

### Error: Permission Denied

```bash
sudo chown -R www-data:www-data /var/www/kasir-pos
sudo chmod -R 775 /var/www/kasir-pos/storage
```

### Error: 500 Internal Server Error

```bash
# Check Laravel log
tail -50 /var/www/kasir-pos/storage/logs/laravel.log

# Check Apache error log
tail -50 /var/log/apache2/kasir-pos-error.log
```

### Error: Database Connection Refused

```bash
# Check MySQL status
sudo systemctl status mysql

# Verify credentials
mysql -u kasir_user -p -e "SHOW DATABASES;"
```

### Cloudflare Tunnel Not Working

```bash
# Check tunnel status
cloudflared tunnel list
cloudflared tunnel info kasir-pos

# Check service logs
sudo journalctl -u cloudflared -f
```

---

**Dibuat untuk Project: Kasir POS**  
**Laravel 11 | PHP 8.3 | Apache2 | MySQL | Cloudflare Tunnel**  
**Ubuntu Server 24.04.2 LTS**
