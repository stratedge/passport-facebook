# Passport-Facebook
Adds support for a "facebook" grant to Laravel's Passport package

## Installation

### Composer

Passport-Facebook can be installed with composer, though it is not yet listed on Packagist.

```sh
composer config repositories.passport-facebook vcs https://github.com/stratedge/passport-facebook.git
composer require stratedge/passport-facebook:dev-master
```

### Service Registration

If you are using Passport-Facebook without any other modifications to the Passport package, then the only required step is to include the Passport-Facebook service provider **instead of** the Passport provider. The Passport-Facebook service provider will register both packages.

```php
//In config/app.php:
'providers' => [
	//...
	Stratedge\PassportFacebook\PassportServiceProvider::class,
	//...
];
```

### Custom Service Registration

If you need to extend the Passport service provider to make other changes to the Passport service, it is still easy to register the Passport-Facebook service as each of its pieces are available as traits.

Assuming you have extended the core Passport service provider, add the following three traits to your custom class:

```php
//...
use \Stratedge\PassportFacebook\Traits\PassportServiceProvider\EnablesFacebookGrant;
use \Stratedge\PassportFacebook\Traits\PassportServiceProvider\LoadsPassportFacebookMigrations;
use \Stratedge\PassportFacebook\Traits\PassportServiceProvider\RegistersUserRepository;
//..
class MyCustomPassportServiceProvider extends PassportService
{
	use EnablesFacebookGrant,
		LoadsPassportFacebookMigrations,
		RegistersUserRepository;
//...
```

Then, in your `boot()` method, call `loadPassportFacebookMigrations()` and `enableFacebookGrant()` after the parent's `boot()`:

```php
	/**
	 * Bootstrap the application services.
    *
    * @return void
    */
	public function boot()
	{
		parent::boot();

		$this->loadPassportFacebookMigrations();

		$this->enableFacebookGrant();
    }
```

And in your `register()` method, call `registerUserRepository()` before the parent's `register()`:

```php
	/**
	 * Register the service provider
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerUserRepository();

		parent::register();
	}
```

### Run Migrations

Once the service is registered, run Artisan's migrate command to add the `facebook_id` columns to the `users` table:

```sh
php artisan migrate
```