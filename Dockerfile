FROM ubuntu:latest

MAINTAINER Tim Rodger <tim.rodger@gmail.com>

EXPOSE 80

RUN apt-get update -qq
RUN apt-get install -y \
    python-software-properties \
    software-properties-common

RUN apt-get update -qq && \
    apt-get install -y \
    nginx \
    php5-cli \
    php5-fpm \
    curl \
    supervisor

# configure server

RUN echo "daemon off;" >> /etc/nginx/nginx.conf
ADD ./build/nginx/default /etc/nginx/sites-enabled/default
ADD ./build/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
ADD ./build/php-fpm/php-fpm.conf /etc/php5/fpm/php-fpm.conf

RUN echo "cgi.fix_pathinfo = 0;" >> /etc/php5/fpm/php.ini

RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/bin/composer

CMD ["supervisord", "--nodaemon"]

# Move application files into place
COPY src/ /home/render/

WORKDIR /home/render

# Install dependencies
RUN composer install --prefer-dist && \
    apt-get clean

USER root
