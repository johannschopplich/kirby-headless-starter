cd {SITE_DIRECTORY}

# Create .env file if it doesn't exist
if [ ! -f .env ] && [ -f .env.production.example ]; then
  cp .env.production.example .env
fi

git pull origin main

# Install Composer dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

{RELOAD_PHP_FPM}

# Clean Kirby cache
rm -rf storage/cache/{SITE_DOMAIN}

echo "🚀 Application deployed!"
