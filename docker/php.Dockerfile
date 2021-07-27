FROM ubuntu:20.04

ENV TZ=Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update -y \
    && apt-get install -y apt-transport-https tar wget curl nano gnupg git zlib1g-dev libxml2-dev libzip-dev \
    && apt install -y default-jre \
    && apt-get install -y software-properties-common \
    && add-apt-repository -y ppa:ondrej/php \
    && apt-get update -y \
    && apt-get install -y php7.4-dev php7.4-fpm php7.4-cli \
     php7.4-mysql php7.4-mysqli php7.4-pdo \
     php7.4-ctype php7.4-zip php7.4-intl php7.4-xml \
     php-gd php7.4-xml php7.4-mbstring php7.4-curl php7.4-amqp php-apcu \
    && apt-get install -y php-pear \
    && pecl install xdebug-2.9.8  \
    && apt-get install -y supervisor \
    && apt-get update -y

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

RUN mkdir /app && mkdir /run/php && mkdir /var/log/php-fpm

COPY ./configs/php/common /etc/php/7.4/cli/
COPY ./configs/php/common /etc/php/7.4/fpm/
COPY ./configs/php/fpm /etc/php/7.4/fpm/

WORKDIR /app

RUN usermod -u 1000 www-data

EXPOSE 9000

CMD ["php-fpm7.4", "-F"]