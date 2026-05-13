#!/bin/bash
set -e

echo "🚀 Starting Laravel container setup..."

# Step 1: Create Laravel project if not already present
if [ -z "$(find /var/www/html -mindepth 1 -not -path '/var/www/html/.gitkeep' -print -quit)" ]; then
  echo "📦 Creating Laravel project (fila-starter)..."
  composer create-project --prefer-dist raugadh/fila-starter:2.0 . --no-interaction
else
  echo "✅ Laravel project already exists. Skipping create-project."
fi

# Step 2: If .env file doesn't exist, create and add necessary environment variables
# Check if the .env file exists
if [ ! -f /var/www/html/.env ]; then
  echo "📄 Creating .env file with environment variables..."

  # Create .env file with the required values
  cat <<EOF > /var/www/html/.env
APP_NAME="${PROJECT_NAME}"
APP_ENV=local
APP_KEY=base64:jU6xg8sp9ia37ypFlTVk1CAFx6MmeXRukO1W987uUzI=
APP_DEBUG=true
APP_TIMEZONE='Asia/Jakarta'
APP_URL="https://${PROJECT_NAME}.test"
ASSET_URL="https://${PROJECT_NAME}.test"
DEBUGBAR_ENABLED=false
ASSET_PREFIX=
# ASSET_PREFIX=/dev/kit/public example in case deployed inside a folder

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mariadb
DB_HOST=db
DB_PORT=3306
DB_DATABASE="${PROJECT_NAME}"
DB_USERNAME=root
DB_PASSWORD=p455w0rd

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
# CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
EOF

fi


# Step 3: Wait for DB connection (host should match DB_HOST in .env)
DB_HOST=$(grep DB_HOST /var/www/html/.env | cut -d '=' -f2)
DB_PORT=$(grep DB_PORT /var/www/html/.env | cut -d '=' -f2)

DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-3306}

echo "⏳ Waiting for database at $DB_HOST:$DB_PORT..."

# Timeout after 30 attempts (1 minute)
RETRIES=30
until nc -z "$DB_HOST" "$DB_PORT"; do
  if [ "$RETRIES" -le 0 ]; then
    echo "❌ Timeout waiting for database. Exiting."
    exit 1
  fi
  echo "Waiting for DB..."
  sleep 2
  RETRIES=$((RETRIES - 1))
done

echo "✅ Database is ready!"

# Step 4: Install dependencies if not already installed
if [ ! -d /var/www/html/vendor ]; then
  echo "📦 Installing composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Step 5: Generate app key if not already present
if [ ! -f /var/www/html/storage/oauth-private.key ]; then
  echo "🔐 Generating Laravel app key..."
  php artisan key:generate --force
fi

# Step 6: Create necessary folders and set permissions
echo "🔧 Fixing permissions..."
mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Step 7: Run database migrations
echo "🗃️ Running migrations..."
php artisan migrate --force

# Step 8: Run custom project init command
echo "🚀 Running project:init..."
php artisan project:init || true

# Step 9: Create storage symbolic link
echo "🔗 Creating storage link..."
php artisan storage:link || true

# Step 10: Start cron
echo "🕒 Starting cron service..."
service cron start

# Step 11: Export development variables from .env to shell
ENV_FILE="/var/www/html/.env"
for VAR in XDEBUG PHP_IDE_CONFIG REMOTE_HOST; do
  if [ -z "${!VAR}" ] && [ -f "$ENV_FILE" ]; then
    VALUE=$(grep ^$VAR= "$ENV_FILE" | cut -d '=' -f 2-)
    if [ -n "$VALUE" ]; then
      sed -i "/$VAR/d" ~/.bashrc
      echo "export $VAR=$VALUE" >> ~/.bashrc
    fi
  fi
done
. ~/.bashrc

# Step 12: Set REMOTE_HOST default if still not defined
if [ -z "$REMOTE_HOST" ]; then
  REMOTE_HOST="host.docker.internal"
  sed -i "/REMOTE_HOST/d" ~/.bashrc
  echo "export REMOTE_HOST=\"$REMOTE_HOST\"" >> ~/.bashrc
  . ~/.bashrc
fi

# Step 13: Toggle Xdebug support
XDEBUG_CONFIG="/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini"

if [ "$XDEBUG" == "true" ] && [ ! -f "$XDEBUG_CONFIG" ]; then
  echo "🐞 Enabling Xdebug..."
  sed -i '/PHP_IDE_CONFIG/d' /etc/cron.d/laravel-scheduler
  if [ -n "$PHP_IDE_CONFIG" ]; then
    echo -e "PHP_IDE_CONFIG=\"$PHP_IDE_CONFIG\"\n$(cat /etc/cron.d/laravel-scheduler)" > /etc/cron.d/laravel-scheduler
  fi
  docker-php-ext-enable xdebug
  {
    echo "xdebug.remote_enable=1"
    echo "xdebug.remote_autostart=1"
    echo "xdebug.remote_connect_back=0"
    echo "xdebug.remote_host=$REMOTE_HOST"
  } >> "$XDEBUG_CONFIG"
elif [ -f "$XDEBUG_CONFIG" ]; then
  echo "🔧 Disabling Xdebug..."
  sed -i '/PHP_IDE_CONFIG/d' /etc/cron.d/laravel-scheduler
  rm -f "$XDEBUG_CONFIG"
fi

echo "✅ Laravel container setup complete. Ready to serve!"

exec "$@"
