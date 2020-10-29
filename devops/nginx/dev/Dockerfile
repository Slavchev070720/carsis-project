ARG NGINX_VERSION
FROM nginx:${NGINX_VERSION}

EXPOSE 80
EXPOSE 443

RUN rm /etc/nginx/conf.d/default.conf
COPY ./config/nginx.conf /etc/nginx/
COPY ./config/server.conf /etc/nginx/conf.d/
COPY ssl/ /etc/ssl/
