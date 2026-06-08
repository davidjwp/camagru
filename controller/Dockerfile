FROM    php:8.2-apache

ARG		SERV_PORT=${SERV_PORT} 

EXPOSE	${SERV_PORT}

ARG		SERV_ADDRESS=${SERV_ADDRESS}
ARG		SERVF=${SERVF}

RUN echo "ServerName ${SERV_ADDRESS}:${SERV_PORT}" >> /etc/apache2/apache2.conf 
RUN echo "Listen ${SERV_ADDRESS}:${SERV_PORT}" >> /etc/apache2/apache2.conf

RUN apt-get update && apt-get upgrade -y && apt-get install -y vim libpng-dev libjpeg-dev && docker-php-ext-configure gd --with-jpeg

CMD ["apache2ctl", "-D", "FOREGROUND"]