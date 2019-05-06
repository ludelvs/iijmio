FROM php:7-fpm

RUN apt-get update -y \
  && apt-get install -y libzip-dev zip libpq-dev libpcre3-dev zlib1g-dev libpng-dev curl libcurl4-openssl-dev vim ruby bundler git wget chromium \
  && docker-php-ext-configure zip --with-libzip \
  && docker-php-ext-install pdo pdo_mysql mysqli mbstring gd curl

COPY . /var/www/html
COPY ./etc/docker/app/.vimrc /root/.vimrc
COPY ./etc/docker/app/docker-entrypoint.sh /bin/docker-entrypoint.sh
COPY ./etc/docker/app/php-fpm.d/docker.conf /usr/local/etc/php-fpm.d/zzz-docker.conf
COPY ./etc/docker/app/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/zzz-www.conf
COPY ./etc/docker/app/php.ini /usr/local/etc/php/php.ini
COPY run.js vendor/run.js

COPY ./etc/docker/app/.miopon-cli /root/.miopon-cli

RUN git clone https://github.com/emasaka/miopon-cli vendor/miopon-cli
RUN cd vendor/miopon-cli && bundle install

RUN curl -sL https://deb.nodesource.com/setup_8.x | bash -
RUN apt-get install -y nodejs npm
RUN cd /var/www/html/vendor && npm install puppeteer

RUN apt-get install -y \
  gconf-service libasound2 libatk1.0-0 libatk-bridge2.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 \
  libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 \
  libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 \
  ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils wget chromium

VOLUME /var/www/html

CMD ["/bin/docker-entrypoint.sh"]
