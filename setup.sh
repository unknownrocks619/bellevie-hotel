#!/bin/bash

# ============================================================
# Bellevie Hotel - Automated Setup Script
# ============================================================
# Run this from inside the project folder:
#   cd /path/to/bellevie && bash setup.sh
# ============================================================

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

step() { echo -e "\n${GREEN}[✔] $1${NC}"; }
warn() { echo -e "${YELLOW}[!] $1${NC}"; }
fail() { echo -e "${RED}[✘] $1${NC}"; exit 1; }

echo ""
echo "=============================================="
echo "  Bellevie Hotel — Setup Script"
echo "=============================================="

# ── 1. Check PHP ─────────────────────────────────
if ! command -v php &> /dev/null; then
    fail "PHP not found. Please install PHP 8.2+ first."
fi
PHP_VER=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo -e "${GREEN}PHP version: $PHP_VER${NC}"

# ── 2. Check Composer ────────────────────────────
if ! command -v composer &> /dev/null; then
    warn "Composer not found. Installing Composer globally..."
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --quiet
    php -r "unlink('composer-setup.php');"
    sudo mv composer.phar /usr/local/bin/composer
fi
step "Composer found: $(composer --version --no-ansi | head -1)"

# ── 3. Install PHP dependencies ──────────────────
step "Running composer install..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# ── 4. Set up .env ───────────────────────────────
step "Configuring .env file..."
# Already pre-configured with bellevie_claude / root / ''

# ── 5. Generate application key ──────────────────
step "Generating application key..."
php artisan key:generate --force

# ── 6. Create MySQL database ─────────────────────
step "Creating MySQL database 'bellevie_claude'..."
mysql -u root --execute="CREATE DATABASE IF NOT EXISTS bellevie_claude CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null \
    || warn "Could not auto-create database. Please create it manually: CREATE DATABASE bellevie_claude;"

# ── 7. Run migrations ────────────────────────────
step "Running database migrations..."
php artisan migrate --force

# ── 8. Seed database ─────────────────────────────
step "Seeding database with demo data..."
php artisan db:seed --force

# ── 9. Storage symlink ───────────────────────────
step "Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || warn "Storage link already exists."

# ── 10. Permissions ──────────────────────────────
step "Setting directory permissions..."
chmod -R 775 storage bootstrap/cache
chown -R $(whoami) storage bootstrap/cache 2>/dev/null || true

# ── 11. Clear & cache config ─────────────────────
step "Optimizing application..."
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear

# ── 12. Done! ────────────────────────────────────
echo ""
echo "=============================================="
echo -e "${GREEN}  ✅ Setup Complete!${NC}"
echo "=============================================="
echo ""
echo "  Start the server:"
echo -e "  ${YELLOW}php artisan serve${NC}"
echo ""
echo "  Then open:"
echo "  🌐 Frontend : http://localhost:8000"
echo "  🔐 Admin    : http://localhost:8000/admin/login"
echo "     Email    : admin@belleviehotel.com"
echo "     Password : admin123"
echo ""
echo "  ⚠️  Don't forget to add your Cloudinary credentials"
echo "     to the .env file before uploading images."
echo "=============================================="
echo ""
