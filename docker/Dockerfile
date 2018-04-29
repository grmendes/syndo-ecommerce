FROM drupal:latest

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN php -r "readfile('http://files.drush.org/drush.phar');" > /usr/local/bin/drush && chmod +x /usr/local/bin/drush

ENV COMPOSER_ALLOW_SUPERUSER="1" PATH="/root/.composer/vendor/bin:$PATH"