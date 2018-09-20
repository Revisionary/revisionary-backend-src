FROM php:7.2.8-apache


# Install Chromium requirements
RUN apt-get update && \
    apt-get install -y  --no-install-recommends \
        gconf-service libasound2 libatk1.0-0 libatk-bridge2.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils wget


# Add the project folders
ADD ./www /var/www/html
ADD ./logs /var/www/logs
ADD ./sessions /var/www/sessions


# Add the self signed certificates
COPY ./certificates/RevisionaryApp.crt /etc/apache2/ssl/RevisionaryApp.crt
COPY ./certificates/RevisionaryApp.key /etc/apache2/ssl/RevisionaryApp.key


# Rewrite the Apache and PHP configurations
COPY ./config/apache-ssl.conf /etc/apache2/sites-enabled/apache-ssl.conf
COPY ./config/php.ini /usr/local/etc/php/


# Add the server name to Apache configuration
RUN echo "ServerName dev.revisionaryapp.com" >> /etc/apache2/apache2.conf


# Install the MySQLi PHP extension
RUN docker-php-ext-install mysqli


RUN a2enmod rewrite
RUN a2enmod ssl
# RUN a2ensite default-ssl
RUN service apache2 restart


# Update the permissions !!! DO AFTER COMPOSER INSTALLATION !
RUN chown -R www-data:www-data /var/www/
RUN chmod -R g+rw /var/www/


# Expose the ports
EXPOSE 80 443