FROM composer:latest
COPY . /var/www/mysql-anonymizer
WORKDIR /var/www/mysql-anonymizer
RUN composer install --no-dev
CMD [ "php", "./bin/mysql-dump-anonymize.php", "--config=./columns_must_anonymize.yml", "--show-progress=0"]