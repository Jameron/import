This package has been built to work with Laravel 5.4.33 and later. 

This package contains methods and traits that can be used with your project, and optinoally you can use the views for your UX. If you are using the admin views, make sure you also require the admin package found here: [Admin package](https://github.com/jameron/admin)

Your composer file would look like so:

```js
        "jameron/admin": "*",
        "jameron/regulator": "*",
```

Some older versions may not be compatible. Let's see if we can't get you up and running in 10 steps. If you are starting fresh, create your laravel application first thing:

    composer create-project --prefer-dist laravel/laravel blog

1) Add the package to your compose.json file:

```json
    "jameron/regulator": "*",
```

```
composer update
```

**NOTE  Laravel 5.5+ users there is auto-discovery so you can ignore steps 2 and 3

2) Update your providers:

```php
        Jameron\Regulator\RegulatorServiceProvider::class,
```

3) Update your Facades:

```php
        'Regulator' => Jameron\Regulator\Facades\RegulatorFacade::class,
```

4) Publish the sass, js, and config:

```
php artisan vendor:publish
```

**NOTE: Select the number that coorelates to the jameron/regulator package

5) Run your migrations:

**NOTE: the Regulator package depends on Laravel Auth so if you haven't set it up yet run:

```
php artisan make:auth
```

**NOTE: DELETE the Auth::routes() from routes/web.php, the Regulator package includes these routes for you.

Alright now go ahead and migrate:

```
php artisan migrate
```

If you get this error

    [Illuminate\Database\QueryException]
    SQLSTATE[42000]: Syntax error or access violation: 1071 Specified key was too long; max key length is 767 bytes (SQL: alter table `users` add unique `users_email_unique`(`email`))``` 

You need to add this to your AppServiceProvider

```php
use Illuminate\Support\Facades\Schema;

function boot()
{
    Schema::defaultStringLength(191);
}
```

6) Seed up the database with two roles and a few permissions

You can call it directly via command line or add it to your applications seeder file:

Added to application seeder

`database/seeds/DatabaseSeeder.php`

```php
$this->call(\Jameron\Regulator\database\seeds\RegulatorSeeder::class);
```

Called via command line:

```php artisan db:seed --class=\\Jameron\\Regulator\\database\\seeds\\RegulatorSeeder```

7) Update your App\User.php

```php

use Jameron\Regulator\Models\Traits\HasRoles;
class User extends Authenticatable
{
	use HasRoles;
```

You will also want these methods on your User model
```php
    public function getRoleAttribute()
    {
        $roles = [];
        foreach($this->roles()->get() as $role) {
            $roles[] = $role->slug;
        }
        return implode(',', $roles);
    } 

    public function getEnabledAttribute()
    {
        return ($this->disabled) ? 'no' : 'yes';
    }

```

8) Make sure you are a pro and use database for your sessions:
```php artisan session:table

php artisan migrate```

9) Subscribe to the login and logout events to update the users last_login and last_logout timestamp on the user model. Add this to app/Providers/EventServiceProvider. 


```php
protected $subscribe = [
\Jameron\Regulator\Listeners\UserEventSubscriber::class,
];
```

9) Update your webpack.mix.js file

```javascript
   .js('resources/assets/regulator/js/RegulatorDependencies.js', 'public/js/Regulator.js')
   .sass('resources/assets/regulator/sass/regulator.scss', 'public/css')
```
10) Make sure you have vuex install

```
npm install vuex --save
```

11) Compile it up:

```
npm run dev
```
