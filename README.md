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

Assuming you have extended the core Passport service provider, add the following five traits to your custom class:

```php
//...
use Stratedge\PassportFacebook\Traits\PassportServiceProvider\LoadsPassportFacebookMigrations;
use Stratedge\PassportFacebook\Traits\PassportServiceProvider\MakesFacebookGrant;
use Stratedge\PassportFacebook\Traits\PassportServiceProvider\RegistersClientRepository;
use Stratedge\PassportFacebook\Traits\PassportServiceProvider\RegistersFacebookCommand;
use Stratedge\PassportFacebook\Traits\PassportServiceProvider\RegistersUserRepository;
//...
class MyCustomPassportServiceProvider extends PassportService
{
	use LoadsPassportFacebookMigrations,
		MakesFacebookGrant,
		RegistersClientRepository,
		RegistersFacebookCommand,
		RegistersUserRepository;
//...
```

Then, in your `boot()` method, call `loadPassportFacebookMigrations()` and `registerFacebookCommand()` after the parent's `boot()`:

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
		
		$this->registerFacebookCommand();
    }
```

Then, in your `register()` method, call `registerUserRepository()` and `registerClientRepository()` before the parent's `register()`:

```php
	/**
	 * Register the service provider
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerUserRepository();
		
		$this->registerClientRepository();

		parent::register();
	}
```

Finally, you must override the `PassportServiceProvider::registerAuthorizationServer()` method in order to enable the Facebook grant:

```php
/**
 * Register the authorization server.
 *
 * @return void
 */
protected function registerAuthorizationServer()
{
    $this->app->singleton(AuthorizationServer::class, function () {
        return tap($this->makeAuthorizationServer(), function ($server) {
            $server->enableGrantType(
                $this->makeAuthCodeGrant(), Passport::tokensExpireIn()
            );

            $server->enableGrantType(
                $this->makeRefreshTokenGrant(), Passport::tokensExpireIn()
            );

            $server->enableGrantType(
                $this->makePasswordGrant(), Passport::tokensExpireIn()
            );

            $server->enableGrantType(
                new PersonalAccessGrant, new DateInterval('P100Y')
            );

            $server->enableGrantType(
                new ClientCredentialsGrant, Passport::tokensExpireIn()
            );

            //Add Facebook grant
            $server->enableGrantType(
                $this->makeFacebookGrant(), Passport::tokensExpireIn()
            );
        });
    });
}
```

If you have other grants to register, add them there as well.

### Run Migrations

Once the service is registered, run Artisan's migrate command to add the `facebook_id` columns to the `users` table:

```sh
php artisan migrate
```

## Including Passport-Facebook with Other Passport Updates

If you use the Passport-Facebook service provider, it will register new `ClientRepository` and `UserRepository` classes which extend the base classes from Passport and add new functions. If you wish to customize either of those classes further, then you just need to make sure that the changes Passport-Facebook depends upon are included with your custom implementations.

### ClientRepository

Because the Facebook grant must be turned on for individual clients, the `ClientRepository` needs to check if the client has access to the Facebook grant. This is done in the `handlesGrant()` method.

Overload the `handlesGrant()` method and include a case for the Facebook grant:

```php
protected function handlesGrant($record, $grantType)
{
    switch ($grantType) {
        case 'authorization_code':
            return ! $record->firstParty();
        case 'personal_access':
            return $record->personal_access_client;
        case 'password':
            return $record->password_client;
        case 'facebook':
            return $record->facebook_client;
        default:
            return true;
    }
}
```

> Note: If you are overriding the `ClientRepository` class, **do not** use Passport-Facebook's service provider as it will register its own `ClientRepository`. Instead, create a custom Passport service provider as explained above and exclude both the `RegistersClientRepository` trait and the call to `registerClientRepository()`.

### UserRepository

In order to verify a user's Facebook token, the `UserRepository` needs to include appropriate verifications methods.

All the required methods are bundled into a trait, which you must include in your custom `UserRepository` class:

```php
use Laravel\Passport\Bridge\UserRepository as BaseUserRepository;
use Stratedge\PassportFacebook\Traits\UserRepository\GetsUserByFacebookToken;

class UserRepository extends BaseUserRepository
{
    use GetsUserByFacebookToken;
}
```

> Note: If you are overriding the `UserRepository` class, **do not** use Passport-Facebook's service provider as it will register its own `UserRepository`. Instead, create a custom Passport service provider as explained above and exclude both the `RegistersUserRepository` trait and the call to `registerUserRepository()`.