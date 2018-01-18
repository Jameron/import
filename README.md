This package has been built to work with Laravel 5.4.33 and later. 

This package contains methods and traits that can be used with your project, and optionally you can use the views or just the view parials for your UX. If you are using the upload view, make sure you also require the admin package found here: [Admin package](https://github.com/jameron/admin)

Your composer file would look like so:

```json
        "jameron/admin": "*",
        "jameron/import": "*",
```

Some older versions may not be compatible. Let's see if we can't get you up and running in 10 steps. If you are starting fresh, create your laravel application first thing:

```bash
    composer create-project --prefer-dist laravel/laravel blog
```

1) Add the package to your compose.json file:

```json
    "jameron/import": "*",
```

```bash
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

```bash

php artisan vendor:publish

```

5) (Only if using Regulator for roles and permissions) Seed the database with import permission and assign permission to admin role

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

6) Update your webpack.mix.js file

```javascript
   .js('resources/assets/import/js/upload.js', 'public/js/Upload.js')
   .sass('resources/assets/import/sass/upload.scss', 'public/css')
```

7) Compile it up:

```bash
npm run dev
```


8) Setup your routes and controllers


```php

Route::group(['middleware' => ['web', 'auth', 'role:admin']], function () {
    Route::get('/import', 'ImportController@getImport');
    Route::post('/import', 'ImportController@postImport');
});
```

```php

use \Jameron\Import\Http\Requests\ImportRequest;

class ImportController extends Controller
{

    public function getImport()
    {
        return view('import::upload');
    }

    public function postImport(ImportRequest $request)
    { 
        $csv = $request->file('csv');
        $headers_cleanup_rules = ['trim','pound_to_word_number','spaces_to_underscores', 'remove_special_characters','lowercase'];
        $import_model = \App\Models\QuizScores::class;
        $validator = \App\Http\Requests\QuizScoreRequest::class;

        $relationships = [
            [
                'create_if_not_found' => true,
                'csv_column' => 'student_id',
                'reference_table' => 'users',
                'reference_field' => 'student_identification_number',
                'reference_primary_key' => 'id',
                'foreign_key' => 'student_id',
                'relationship' => 'belongsTo',
                'model' => \App\Models\User::class,
                'validator' => \App\Http\Requests\UserRequest::class,
                'roles' => ['student'], // this only works for new users with the regulator package installed
                'extra_columns' => [
                    [
                        'column' => 'student_name',
                        'maps_to' => ['first_name','last_name'],
                        'explode_on' => ' '
                    ],
                    [
                        'column' => 'email',
                        'maps_to' => 'email',
                    ]
                ],
                'append_data' => [
                    'password' => \Hash::make('ChangeIt!')
                ]
            ],
            [
                'csv_column' => 'test_name',
                'reference_table' => 'tests',
                'reference_field' => 'name', // This assumes that the name field on the tests table has a rule that forces unique
                'reference_primary_key' => 'id', 
                'foreign_key' => 'test_id',
                'model' => \App\Models\Tests::class,
                'validator' => \App\Http\Requests\TestRequest::class,
            ] 
        ];
        
    }

}
```
