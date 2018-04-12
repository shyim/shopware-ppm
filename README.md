# Shopware with [PHP Process Manager](https://github.com/php-pm/php-pm)

# This project is very experimental!

### Why?
* [Performance boost up to 15x (due no bootstrap)](https://github.com/php-pm/php-pm#features)


### Problems

* Shopware has some memory leaks
* Shopware backend auth does not work


### How to setup

* Checkout this repository
* Install shopware (composer project setup)
* [Setup PHP-PM](https://github.com/php-pm/php-pm/wiki/Use-without-Docker)
* Start ppm ``./vendor/bin/ppm start --bootstrap=Shyim\\PPM\\Bootstraps\\Shopware --static-directory=. --port 80``