ElecAPI
=======

Init project
------------

First create your app/parameters.yml file

Install vendors via composer

For the following commands, enter bash by running:

    docker-compose exec php bash

Data sample, fixtures and tests
-------------------------------

To access phpmyadmin go to: http://electronic-store.dev:8080/

To load files/electronic-catalog.json file run:
    
    sf3 elec:load-catalog
     
To load fixtures run:

    sf3 doctrine:fixtures:load
    
To execute tests 
    
* check hostname parameter in app/config_test.yml 
* create your phpunit.xml
    
        cp phpunit.xml.dist phpunit.xml

* run:

        vendor/phpunit/phpunit/phpunit
