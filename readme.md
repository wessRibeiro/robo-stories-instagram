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
composer require league/flysystem-aws-s3-v3
````

### Lembretes do ambiente Louder 1.0
````
* setConnectionsHub() é uma função que se encontra no arquivo helper e é executada no 
AppServiceProvider. Essa função busca todas as conexões no louderHub e insere todas
em tempo de execução no config.database. Tornando .envs desnecessários e as conexões 
cadastraveis
````
