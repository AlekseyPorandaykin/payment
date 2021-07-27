FROM nginx:1.20
RUN apt-get update -y \
   && apt-get install -y nano

RUN touch /var/log/nginx/php_error.log /var/log/nginx/php_access.log

COPY ./configs/nginx/default.conf /etc/nginx/conf.d/