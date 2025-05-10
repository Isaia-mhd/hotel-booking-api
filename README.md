#Step to use this REST API


1-> git clone https://github.com/Isaia-mhd/hotel-booking-api.git

2-> cd hotel-booking-api

3-> composer install

4-> cp .env.example .env

5-> php artisan key:generate

6-> php artisan migrate  (--seed) if you want fake data

7-> composer require stripe/stripe-php

8-> php artisan serve

Note: 

Don't forget to put BREVO_API_KEY in .env file, and check if the ip address is authorized in brevo.

STRIPE_SECRET/STRIPE PUBLIC in .env file
