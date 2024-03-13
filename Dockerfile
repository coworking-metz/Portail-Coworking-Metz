FROM wordpress:5.9.3

# Install Git
RUN apt-get update && \
    apt-get install -y git && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# remplacer le code de wordpress par le contenu de notre site
RUN rm -rf /var/www/html/

RUN git clone https://github.com/coworking-metz/Portail-Coworking-Metz /var/www/html/

COPY wp-config.php /var/www/html/wp-config.php

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Verify that WP-CLI is installed successfully
RUN wp --info
