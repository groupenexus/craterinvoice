# Compile the front-end from source so the image never ships stale assets
FROM node:22-bookworm-slim AS assets

WORKDIR /build
# yarn.lock is the lock file that matches package.json (package-lock.json is out of sync)
RUN npm install -g yarn@1.22.22
COPY package.json yarn.lock ./
RUN yarn install --frozen-lockfile
COPY . .
RUN yarn build

FROM php:8.3-fpm

# Arguments defined in docker-compose.yml; defaults keep builds that don't pass them working
ARG user=crater-user
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libmagickwand-dev \
    mariadb-client \
    nginx

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Replace the default nginx config; it serves HTTP on 8080 and hands PHP to php-fpm on 9000
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Set working directory
WORKDIR /var/www

# Bake the application code into the image instead of relying on a mounted volume
COPY --chown=$user:$user . /var/www

# Replace the committed build output with the one compiled in the assets stage
COPY --from=assets --chown=$user:$user /build/public/build /var/www/public/build

# WORKDIR creates /var/www as root; the app user needs to write vendor/ into it
RUN chown $user:$user /var/www

USER $user

# Production image: dev-only packages (require-dev) are left out
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

EXPOSE 8080

# php-fpm runs in the background; nginx stays in the foreground so the container lives with it
CMD ["sh", "-c", "php-fpm -D && exec nginx -g 'daemon off;'"]
