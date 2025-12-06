FROM richarvey/nginx-php-fpm:latest

# Copy your code to the container
COPY . /var/www/html

# ✅ FIX 1: Install Dependencies (Composer)
# This downloads the missing libraries
RUN composer install --no-dev --optimize-autoloader

# ✅ FIX 2: Set Permissions
# Laravel needs permission to write to storage/logs
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Configuration
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV APP_ENV production
ENV APP_DEBUG true
ENV LOG_CHANNEL stderr

# Allow composer to run as root if needed later
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]