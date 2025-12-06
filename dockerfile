FROM richarvey/nginx-php-fpm:latest

# Copy your code to the container
COPY . /var/www/html

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# ✅ FIX: Add '--ignore-platform-reqs' to stop version errors
# This tells Composer: "Just install the libraries, don't complain about PHP version"
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Fix permissions so Laravel can write to logs
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Configuration
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV APP_ENV production
ENV APP_DEBUG true
ENV LOG_CHANNEL stderr

CMD ["/start.sh"]