FROM ubuntu:latest

MAINTAINER Tim Rodger <tim.rodger@gmail.com>

EXPOSE 80

RUN apt-get update -qq && \
    apt-get install -y \
    nginx \
    libxml2 \
    php5 \
    php5-cli \
    php5-fpm \
    curl


# Setup nginx
ADD build/default /etc/nginx/sites-available/default
RUN echo "cgi.fix_pathinfo = 0;" >> /etc/php5/fpm/php.ini
RUN echo "daemon off;" >> /etc/nginx/nginx.conf

RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer

CMD ["php5-fpm && nginx"]

# Move application files into place
COPY src/ /home/render/

WORKDIR /home/render

# Install dependencies
RUN composer install --prefer-dist && \
    apt-get clean
