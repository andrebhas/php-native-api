# Simple Payment API 

Simple payment API with PHP Native.


# Requirements

- PHP 8.2
- MySql
- Composer

## Build
    git clone https://github.com/andrebhas/php-native-api.git
    cd php-native-api
    cp src/.env.example src/.env
    composer install
    
    

## Setup Environment

- Create a new mysql database
- Edit .env file on the `src/.env`, Fill in all the blank variables according to your environment such as database and app key

## CLI
### Migrate DB

    php src/run/migration.php

### Seeder Data

    php src/run/seeder.php

### Update Payment
> reference_id = payment reference id 
> status = pending, paid, failed

    php src/run/payment-status-update.php reference_id status

## Running API
> You can use any port other than 8000 

    php -S localhost:8000 -t src/public 

## API Documentation
https://documenter.getpostman.com/view/1013348/2s8Z75S9p6#05a3ad3e-a3a7-41cd-8724-878f7dd30066
> You can copy to your Postman

![enter image description here](https://i.postimg.cc/m2HZJwL9/Screen-Shot-2023-01-08-at-08-16-55.png)