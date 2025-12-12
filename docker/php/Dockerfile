# =============================================================================
# IMMAGINE BASE
# =============================================================================
# php:8.3-fpm-alpine significa:
# - php:8.3 = versione PHP
# - fpm = PHP-FPM (FastCGI Process Manager) - gestisce richieste da Nginx
# - alpine = Linux Alpine, distribuzione ultra-leggera (~5MB vs ~100MB Ubuntu)
FROM php:8.3-fpm-alpine

# =============================================================================
# INSTALLAZIONE DIPENDENZE DI SISTEMA
# =============================================================================
# apk = package manager di Alpine (come apt per Ubuntu)
# --no-cache = non salva la cache dei pacchetti (immagine più leggera)
RUN apk add --no-cache \
    # Strumenti per compilare estensioni PHP (gcc, make, autoconf...)
    $PHPIZE_DEPS \
    # Librerie PostgreSQL (header files per compilare pdo_pgsql)
    postgresql-dev \
    # Librerie ICU per internazionalizzazione
    icu-dev \
    # Utility varie
    git \
    zip \
    unzip \
    curl-dev

# =============================================================================
# INSTALLAZIONE ESTENSIONI PHP
# =============================================================================
# docker-php-ext-install = script incluso nell'immagine ufficiale PHP
# Compila e installa estensioni PHP
RUN docker-php-ext-install \
    # PDO = PHP Data Objects (interfaccia DB astratta)
    pdo \
    # Driver PDO specifico per PostgreSQL
    pdo_pgsql \
    # Intl = Internazionalizzazione (formattazione date, numeri, collation)
    intl \
    # OPcache = Cache del bytecode PHP (ENORME boost performance)
    opcache \
    # BCMath = Matematica di precisione arbitraria
    bcmath

# =============================================================================
# ESTENSIONI PECL (repository estensioni community)
# =============================================================================
# Redis non è incluso in PHP, va installato da PECL
RUN pecl install redis \
    && docker-php-ext-enable redis

# =============================================================================
# COMPOSER
# =============================================================================
# Invece di scaricarlo, lo copiamo dall'immagine ufficiale composer
# --from=composer:2 = usa l'immagine composer:2 come sorgente
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# =============================================================================
# CONFIGURAZIONE PHP PERSONALIZZATA
# =============================================================================
# Copia il nostro php.ini nella directory conf.d
# I file in conf.d vengono caricati automaticamente
COPY php.ini /usr/local/etc/php/conf.d/app.ini

# =============================================================================
# WORKING DIRECTORY
# =============================================================================
# Directory di default per tutti i comandi successivi
WORKDIR /var/www/html

# =============================================================================
# PORTA
# =============================================================================
# PHP-FPM ascolta su porta 9000 (protocollo FastCGI, NON HTTP!)
# Nginx farà da proxy HTTP -> FastCGI
EXPOSE 9000

# =============================================================================
# COMANDO DI AVVIO
# =============================================================================
# Avvia PHP-FPM (deve restare in foreground per Docker)
CMD ["php-fpm"]
