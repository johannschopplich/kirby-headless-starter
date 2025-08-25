cd {SITE_DIRECTORY}

# Create .env file if it doesn't exist
if [ ! -f .env ] && [ -f .env.production.example ]; then
  cp .env.production.example .env
fi

git pull origin main

# Install Composer dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

{RELOAD_PHP_FPM}

# Ensure NVM is loaded
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

# Enable Corepack and install pnpm
corepack enable

# Install dependencies and build the project
if [ -f package-lock.json ]; then
  npm ci && npm run build
elif [ -f pnpm-lock.yaml ]; then
  pnpm i && pnpm run build
fi

# Clean Kirby cache
rm -rf storage/cache/{SITE_DOMAIN}

echo "🚀 Application deployed!"
