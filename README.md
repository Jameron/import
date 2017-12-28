This package has been built to work with Laravel 5.4.33 and later. 

This package contains methods and traits that can be used with your project, and optinoally you can use the views for your UX. If you are using the admin views, make sure you also require the admin package found here: [Admin package](https://github.com/jameron/admin)

Your composer file would look like so:

```js
        "jameron/admin": "*",
        "jameron/import": "*",
```

Some older versions may not be compatible. Let's see if we can't get you up and running in 10 steps. If you are starting fresh, create your laravel application first thing:

    composer create-project --prefer-dist laravel/laravel blog

1) Add the package to your compose.json file:

```json
    "jameron/imports": "*",
```

```
composer update
```

**NOTE  Laravel 5.5+ users there is auto-discovery so you can ignore steps 2 and 3

2) Update your providers:

```php
        Jameron\Regulator\ImportsServiceProvider::class,
```

3) Update your Facades:

```php
        'Imports' => Jameron\Regulator\Facades\ImportsFacade::class,
```

4) Publish the views and config:

```
php artisan vendor:publish
```

5) Seed the database with import permission and assign permission to admin role

You can call it directly via command line or add it to your applications seeder file:

Added to application seeder

`database/seeds/DatabaseSeeder.php`

```php
$this->call(\Jameron\Import\database\seeds\ImportSeeder::class);
```

Called via command line:

```bash
php artisan db:seed --class=\\Jameron\\Import\\database\\seeds\\ImportSeeder
```
