FROM composer:1 AS build-env
COPY . /build
WORKDIR /build
RUN composer install --no-dev

FROM php:7.4-cli-alpine
COPY --from=build-env /build /anonymizer
ENTRYPOINT ["php", "-d", "display_errors=stderr", "/anonymizer/bin/mysql-dump-anonymize.php"]
CMD ["--show-progress=0", "--config=/anonymizer/columns_must_anonymize.yml"]
