FROM php:8.2

COPY . /usr/src/myApp

WORKDIR /usr/src/myApp

EXPOSE 8080

CMD [ "php", "-S", "0.0.0.0:8080" ]
