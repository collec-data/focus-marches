FROM php:8.5.12-apache-trixie

EXPOSE 80
WORKDIR /app

RUN apt-get update -qq && \
    apt-get install -qy \
    libicu-dev \
    libonig-dev \
    locales && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN sed -i -e 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen && \
    sed -i -e 's/# fr_FR.UTF-8 UTF-8/fr_FR.UTF-8 UTF-8/' /etc/locale.gen && \
    dpkg-reconfigure --frontend=noninteractive locales && \
    update-locale LANG=fr_FR.UTF-8
ENV LC_ALL fr_FR.UTF-8
ENV LANGUAGE fr_FR:en_US:fr
ENV LANG fr_FR.UTF-8

# PHP Extensions
#RUN docker-php-ext-install -j$(nproc) opcache pdo_mysql
#RUN docker-php-ext-install curl
RUN docker-php-ext-configure intl
RUN docker-php-ext-install mysqli pdo_mysql mbstring gettext intl 

COPY conf/php.ini /usr/local/etc/php/conf.d/app.ini

# Apache
COPY errors /errors
COPY conf/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY conf/apache.conf /etc/apache2/conf-available/z-app.conf
COPY ui /app
RUN ln -s /app /app/focus-marches
RUN a2enmod rewrite remoteip && \
    a2enconf z-app

COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod u+x /docker-entrypoint.sh
ENTRYPOINT ["/docker-entrypoint.sh", "apache2-foreground"]