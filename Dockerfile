FROM composer:1
COPY . /var/www/mysql-anonymizer
WORKDIR /var/www/mysql-anonymizer
RUN composer install --no-dev
ENTRYPOINT ["php", "/var/www/mysql-anonymizer/bin/mysql-dump-anonymize.php"]
CMD ["--show-progress=0", "--config=./columns_must_anonymize.yml"]
