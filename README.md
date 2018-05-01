# Prestashop

### Commands

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~$ docker-compose up -d
$ docker-compose exec nodejs npm --prefix themes/customized/_dev install
$ docker-compose exec php-fpm composer install
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


### Installation

Import MySQL dump :
````bash
docker exec -it prestashop-mariadb bash
mysql -u root -proot prestashop < bin/prestashop
````

To use on ``localhost`` or any other host, replace the 
docker-compose webserver port by ``80:80`` and run the following 
SQL Query : 
````sql
UPDATE `ps_shop_url` SET `domain` = 'localhost', `domain_ssl` = 'localhost' WHERE `id_shop_url` = 1;
````

To update the page title : 
````sql
UPDATE `ps_configuration` SET `value` = 'MyPageTitle' WHERE `id_configuration` = 231
````

### Admin 
``/dashboard``

Email: admin@prestashop.com
Password: password
