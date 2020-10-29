#!/bin/bash
#Generation of public and private key for JWT
openssl genrsa -passout pass:${JWT_PASSPHRASE} -out /var/www/app/config/jwt/private.pem -aes256 4096
openssl rsa -passin pass:${JWT_PASSPHRASE} -pubout -in /var/www/app/config/jwt/private.pem -out /var/www/app/config/jwt/public.pem
#Database migration and DataFixture load
php /var/www/app/bin/console doctrine:migrations:migrate --no-interaction
php /var/www/app/bin/console doctrine:fixtures:load --append