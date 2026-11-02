FROM php:8.2-apache-bookworm
COPY public/ /var/www/html/
RUN a2enmod headers && printf 'ServerTokens Prod\nServerSignature Off\n' > /etc/apache2/conf-enabled/security-custom.conf
EXPOSE 80
