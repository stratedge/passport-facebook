<?php

namespace Stratedge\PassportFacebook;

use Laravel\Passport\PassportServiceProvider as BasePassportServiceProvider;
use Stratedge\PassportFacebook\Traits\PassportServiceProvider\LoadsPassportFacebookMigrations;
use Stratedge\PassportFacebook\Traits\PassportServiceProvider\MakesFacebookGrant;
use Stratedge\PassportFacebook\Traits\PassportServiceProvider\RegistersClientRepository;
use Stratedge\PassportFacebook\Traits\PassportServiceProvider\RegistersFacebookCommand;
use Stratedge\PassportFacebook\Traits\PassportServiceProvider\RegistersUserRepository;

class PassportServiceProvider extends BasePassportServiceProvider
{
    use LoadsPassportFacebookMigrations,
        MakesFacebookGrant,
        RegistersClientRepository,
        RegistersFacebookCommand,
        RegistersUserRepository;

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

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerUserRepository();

        $this->registerClientRepository();

        parent::register();
    }


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

                $server->enableGrantType(
                    $this->makeFacebookGrant(), Passport::tokensExpireIn()
                );
            });
        });
    }
}
