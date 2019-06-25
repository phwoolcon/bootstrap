FROM alpine:3.9

RUN echo "http://mirror.xtom.com.hk/alpine/v3.9/main" > /etc/apk/repositories; \
    echo "http://mirror.xtom.com.hk/alpine/v3.9/community" >> /etc/apk/repositories; \
    apk update; apk upgrade; \
    apk add --no-cache bash coreutils nginx \
    php7 php7-curl php7-fileinfo php7-fpm php7-gd php7-json php7-mbstring php7-opcache php7-openssl \
    php7-pdo php7-pdo_mysql php7-pecl-redis php7-phalcon php7-simplexml php7-sodium php7-tokenizer php7-xml php7-zip \
    composer;
RUN mkdir -p /srv/http/app/config/production /run/nginx /etc/nginx/snippets; \
    printf "alias ls='ls --color'\n\
alias ll='ls -la --group-directories-first'\n\
alias lh='ll -h'\n\
alias ping='ping -c4'\n\
alias apk-add='apk add --no-cache'\n\
alias apk-del='apk del'\n\
alias apk-update='apk update'\n\
alias apk-upgrade='apk upgrade'\n\
" > /root/.bashrc; \
    printf "\n\
fastcgi_split_path_info ^(.+?\.php)(/.*)\$;\n\
try_files \$fastcgi_script_name =404;\n\
set \$path_info \$fastcgi_path_info;\n\
fastcgi_param PATH_INFO \$path_info;\n\
fastcgi_index index.php;\n\
include fastcgi.conf;\n" > /etc/nginx/snippets/fastcgi-php.conf; \
    printf "\n\
log_format new '\$remote_addr，\$host，\$time_iso8601，\$status，'\n\
    '\$request_time，\$request_length，\$bytes_sent，\$http_referer，\$request，\$http_user_agent';\n\
" > /etc/nginx/conf.d/00-log-formats.conf; \
    printf "\n\
server {\n\
    listen 80 default_server;\n\
    server_name     _;\n\
    root /srv/http/public;\n\
    index  index.php index.html index.htm;\n\n\
    access_log off;\n\
    error_log /var/log/nginx/phwoolcon_error.log;\n\n\
    location / {\n\
        try_files \$uri \$uri/ /index.php?\$query_string;\n\
    }\n\n\
    location ~ \.php\$ {\n\
        include snippets/fastcgi-php.conf;\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        access_log /var/log/nginx/phwoolcon_access.log new buffer=128k flush=5s;\n\
    }\n\
}\n" > /etc/nginx/conf.d/default.conf; \
    printf "#!/usr/bin/env bash\n\
cd /srv/http;\n\
mkdir -p storage/{cache,logs,session} public/{assets,static,uploads}\n\
bin/dump-autoload > /dev/null;\n\
bin/cli migrate:up\n\
bin/dump-autoload;\n\
chown -R nobody:nobody storage/ public/static/ public/uploads/;\n\
php-fpm7 -D;\n\
nginx -g 'daemon off;'" > /entrypoint.sh; \
    chmod +x /entrypoint.sh;
COPY . /srv/http/
EXPOSE 80
CMD ["/bin/bash"]
ENTRYPOINT ["/entrypoint.sh"]
