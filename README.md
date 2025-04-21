#Step to use this REST API

1-> git clone <link-of-repo>
2-> cd hotel-boking-api
3-> composer install
4-> cp .env.example .env
5-> php artisan key:generate
6-> php artisan migrate  (--seed) if you want faka data
7-> composer require stripe/stripe-php
8-> php artisan serve
