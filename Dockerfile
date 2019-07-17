FROM alpine:3.9

ARG ALPINE_REPO=""
ARG NGINX="1"
ARG DEBUG_TOOLS="0"
ARG UTILS_BASE="https://raw.githubusercontent.com/phwoolcon/docker-utils/master"
ENV ENV="/etc/profile"
RUN wget ${UTILS_BASE}/alpine/aliases.sh -O /etc/profile.d/aliases.sh; \
    wget ${UTILS_BASE}/alpine/pick-mirror -O /usr/local/bin/pick-mirror; \
    wget ${UTILS_BASE}/alpine/determine-fpm-workers -O /usr/local/bin/determine-fpm-workers; \
    chmod +x /usr/local/bin/*; \
    pick-mirror v3.9; \
    apk update; apk upgrade; \
    apk add --no-cache bash coreutils \
    php7 php7-curl php7-ctype php7-fileinfo php7-fpm php7-gd php7-json php7-mbstring php7-opcache php7-openssl \
    php7-pdo php7-pdo_mysql php7-pecl-redis php7-phalcon php7-simplexml php7-sodium php7-tokenizer php7-xml php7-zip \
    composer;
RUN ( [ "$DEBUG_TOOLS" = "1" ] ) && { \
        apk add --no-cache vim htop iftop iotop; \
        wget ${UTILS_BASE}/dusort -O /usr/local/bin/dusort; \
        chmod +x /usr/local/bin/dusort; \
        >&2 echo "Debug tools installed, by demand"; \
    } || echo "";
RUN mkdir -p /srv/http/app/config/production /mnt/data; \
    printf "php-fpm7 -F;\n" > /start.sh; \
    ( [ "$NGINX" = "1" ] ) && { \
        apk add --no-cache nginx; \
        mkdir -p /run/nginx; \
        wget ${UTILS_BASE}/alpine/nginx/00-log-formats.conf -O /etc/nginx/conf.d/00-log-formats.conf; \
        wget ${UTILS_BASE}/alpine/nginx/default.conf -O /etc/nginx/conf.d/default.conf; \
        sed -i 's|/var/log|/mnt/data/log|g' /etc/nginx/nginx.conf; \
        printf "mkdir -p /mnt/data/log/nginx;\n\
php-fpm7 -D;\n\
nginx -g 'daemon off;'\n" > /start.sh; \
    } || >&2 echo "Nginx not installed, by demand";
RUN echo 'error_log = /mnt/data/log/php7/error.log' > /etc/php7/php-fpm.d/00-log.conf; \
    sed -i 's|expose_php = On|expose_php = Off|g' /etc/php7/php.ini; \
    sed -i 's|/var/log|/mnt/data/log|g; s|127.0.0.1:9000|0.0.0.0:9000|g; s|pm = dynamic|pm = static|g' \
    /etc/php7/php-fpm.d/www.conf; \
    printf "#!/usr/bin/env bash\n\
determine-fpm-workers;\n\
mkdir -p /mnt/data/{config,log/php7,log/app};\n\
cd /srv/http;\n\
mkdir -p storage/{cache,session} public/{assets,static,uploads};\n\
ln -snf /mnt/data/config/*.php app/config/production/;\n\
ln -snf /mnt/data/log/app storage/logs;\n\
bin/dump-autoload > /dev/null;\n\
bin/cli migrate:up;\n\
bin/dump-autoload;\n\
chown -R nobody:nobody storage/ public/{static,uploads} /mnt/data/log/app;\n" > /entrypoint.sh; \
    cat /start.sh >> /entrypoint.sh; \
    rm /start.sh; \
    chmod +x /entrypoint.sh;
COPY . /srv/http/
VOLUME /mnt/data
EXPOSE 80 9000
ENTRYPOINT ["/entrypoint.sh"]
