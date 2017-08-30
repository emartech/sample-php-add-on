FROM emarsys/php7-1-apache

COPY conf/app.conf /etc/apache2/sites-available/app.conf

RUN a2ensite app
RUN a2enmod rewrite
