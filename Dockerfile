FROM php:8.2-apache

# Activer mod_rewrite
RUN a2enmod rewrite

# Copier tous les fichiers du projet
COPY . /var/www/html/

# Créer le dossier backend-amex s'il n'existe pas + permissions
RUN mkdir -p /var/www/html/backend-amex && \
    chmod -R 777 /var/www/html/backend-amex && \
    echo "[]" > /var/www/html/backend-amex/redirections.json && \
    chmod 666 /var/www/html/backend-amex/redirections.json && \
    echo "[]" > /var/www/html/backend-amex/dossier-amex.json && \
    chmod 666 /var/www/html/backend-amex/dossier-amex.json

# Exposer le port 80
EXPOSE 80