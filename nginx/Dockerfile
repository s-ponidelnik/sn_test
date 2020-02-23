FROM alpine:3.10

LABEL maintainer="Ponidielnik Serhii <s.ponidelnik@gmail.com>"

RUN apk add --update --no-cache nginx

COPY nginx.conf /etc/nginx/
COPY sites-available/frontend.localhost /etc/nginx/sites-available/frontend.localhost
COPY sites-available/backend.localhost /etc/nginx/sites-available/backend.localhost
RUN mkdir /etc/nginx/sites-enabled/
RUN ln -s /etc/nginx/sites-available/frontend.localhost /etc/nginx/sites-enabled/
RUN ln -s /etc/nginx/sites-available/backend.localhost /etc/nginx/sites-enabled/
RUN echo "upstream php-upstream { server php:9001; }" > /etc/nginx/conf.d/upstream.conf

RUN adduser -D -g '' -G www-data www-data
RUN cd /var/www/ && ls
CMD ["nginx"]

EXPOSE 80
EXPOSE 443