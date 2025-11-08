#!/bin/bash
set -e

echo "--- 🚀 Deployment Started ---"

APP_ROOT="/var/www/31.220.52.42/html"
COMPOSER="/usr/local/bin/composer"
NODE_BIN="/usr/bin/npm"
PHP_BIN="/usr/bin/php"
WWW_USER="www-data"

cd $APP_ROOT

# --- Git Fixes START ---
echo "1. Stashing local changes and cleaning untracked files..."
# Unstaged পরিবর্তনগুলো স্ট্যাশ করা হচ্ছে, যেন pull/rebase এ বাধা না দেয়
git stash || true
# Git ট্র্যাক করে না এমন ফাইলগুলো (যেমন local cache) মুছে ফেলা হচ্ছে
git clean -df || true
# --- Git Fixes END ---

echo "2. Pulling latest code..."
# Pulling latest code with rebase
git pull origin main --rebase

echo "3. Installing Composer dependencies..."
$COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

echo "4. Installing & building frontend..."
$NODE_BIN ci
$NODE_BIN run build

echo "5. Clearing and optimizing Laravel cache..."
$PHP_BIN artisan down || true
$PHP_BIN artisan migrate --force
$PHP_BIN artisan optimize:clear
$PHP_BIN artisan optimize
$PHP_BIN artisan up

echo "6. Fixing permissions..."
sudo chown -R $WWW_USER:$WWW_USER $APP_ROOT/storage $APP_ROOT/bootstrap/cache
sudo chmod -R 775 $APP_ROOT/storage $APP_ROOT/bootstrap/cache

echo "--- ✅ Deployment Finished Successfully! ---"