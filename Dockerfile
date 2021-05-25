FROM php:8.0.6

COPY --from=composer /usr/bin/composer /usr/bin/composer
EXPOSE 80
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN echo "composer install && sleep 5 && php /app/bin/console.php reset test && php /app/bin/console.php reset && cron && php -S 0.0.0.0:80 -t /app/public" > /entrypoint.sh
RUN chmod +x /entrypoint.sh

ADD cron/crontab /etc/cron.d/gitrub-cron
RUN chmod 0644 /etc/cron.d/gitrub-cron
RUN touch /var/log/cron.log

RUN apt-get update
RUN apt-get -y install cron

WORKDIR /app
