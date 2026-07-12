FROM    php:8.2-apache

ARG		SERV_PORT=${SERV_PORT} 

EXPOSE	${SERV_PORT}

ARG		SERV_ADDRESS=${SERV_ADDRESS}
ARG		SERVF=${SERVF}

COPY php.ini /usr/local/etc/php/conf.d/mail.ini
COPY ssmtp.conf /etc/ssmtp/ssmtp.conf

RUN echo "ServerName ${SERV_ADDRESS}" >> /etc/apache2/apache2.conf 
RUN echo "Listen ${SERV_PORT}" >> /etc/apache2/apache2.conf
RUN cat >> /etc/apache2/apache2.conf << EOF
DocumentRoot /var/www/html
DirectoryIndex index.php index.html
<Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
</Directory>
EOF
RUN cat > /etc/apache2/sites-enabled/000-default.conf << EOF
<VirtualHost *:${SERV_PORT}>
    ServerName ${SERV_ADDRESS}
    DocumentRoot /var/www/html
    DirectoryIndex index.php index.html

    ErrorLog /var/log/apache2/error.log
    CustomLog /var/log/apache2/access.log combined
</VirtualHost>
EOF

RUN apt-get update && apt-get upgrade -y && apt-get install -y vim iproute2 libpng-dev libjpeg-dev
RUN apt-get install -y ssmtp inetutils-ping 
RUN docker-php-ext-configure gd --with-jpeg
RUN docker-php-ext-install pdo pdo_mysql

CMD ["apache2ctl", "-D", "FOREGROUND"]