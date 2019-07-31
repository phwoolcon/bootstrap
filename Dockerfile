FROM phwoolcon/phwoolcon:latest

ARG ALPINE_REPO=""
ARG NGINX="1"
RUN pick-mirror v3.9; \
    ( [ "$NGINX" = "1" ] ) || { \
        sed -i 's|php-fpm7 -D|php-fpm7 -F|g; /^nginx -g/d' /entrypoint.sh; \
    }
COPY . /srv/http/
