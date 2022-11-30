FROM php:8.0-cli
RUN apt-get update -yqq

RUN apt-get update -yqq \
    && apt-get install -y vim zip dos2unix curl wget git nodejs npm default-mysql-client iputils-ping


###########################################################################
# Install Composer
###########################################################################
WORKDIR /root
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
COPY ./install-composer.sh .
RUN dos2unix install-composer.sh && ./install-composer.sh && rm ./install-composer.sh


############################################################################
## Install XDebug
############################################################################
RUN pecl channel-update pecl.php.net
ARG XDEBUG_VERSION
RUN pecl -q install xdebug-3.1.6 \
    && docker-php-ext-enable xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini


###########################################################################
# Install SSHD
###########################################################################
RUN apt-get install -y openssl openssh-server \
      # fix ssh start up bug
      # @see https://github.com/ansible/ansible-container/issues/141
    && mkdir /var/run/sshd
COPY ./ssh/insecure_id_rsa.pub /root/insecure_id_rsa.pub
RUN mkdir -p /root/.ssh \
    && cat /root/insecure_id_rsa.pub >> /root/.ssh/authorized_keys \
    && rm -rf /root/insecure_id_rsa.pub

############################################################################
## Install Symfony
############################################################################
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt install symfony-cli

###########################################################################
# Install MySQL Client Libraries
###########################################################################
RUN docker-php-ext-install mysqli pdo pdo_mysql


WORKDIR /var/www

CMD ["/usr/sbin/sshd", "-D"]
