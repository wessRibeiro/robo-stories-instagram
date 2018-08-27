### COMO INSTALAR O PROJETO (utilize sudo caso necessário)
```
* chmod 777 -R storage
* chmod 777 -R bootstrap/cache;
* composer install
* cp .env.exemple .env (se não tiver)
* php artisan key:generate
* php artisan migrate:install
* php artisan migrate
```

### composer  require
````
composer require nesbot/carbon

````