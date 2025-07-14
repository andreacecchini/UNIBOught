# Usa l'immagine ufficiale di PHP con Apache
FROM php:8.2-apache

# Installa le estensioni PHP necessarie
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Abilita mod_rewrite per Apache
RUN a2enmod rewrite

# Esponi la porta 80 per il web server
EXPOSE 80