FROM php:7.2.8-apache


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
RUN echo "ServerName revisionaryapp.com" >> /etc/apache2/apache2.conf


# Install necessary PHP Extensions (intl for intljeremykendall/php-domain-parser)
RUN apt-get -y update \
    && apt-get install -y libicu-dev\
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl mysqli


# Activate the rewrite engine and SSL
RUN a2enmod rewrite
RUN a2enmod ssl
RUN service apache2 restart


# Update the permissions
RUN chown -R www-data:www-data /var/www/
RUN find /var/www/ -type f -exec chmod 644 {} \;
RUN find /var/www/ -type d -exec chmod 755 {} \;
#RUN chmod -R g+rw /var/www/


# Expose the ports
EXPOSE 80 443