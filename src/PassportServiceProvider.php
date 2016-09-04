<?php

namespace Stratedge\PassportFacebook;

use Laravel\Passport\PassportServiceProvider as BasePassportServiceProvider;
use Stratedge\PassportFacebook\Traits\PassportServiceProvider\EnablesFacebookGrant;
use Stratedge\PassportFacebook\Traits\PassportServiceProvider\LoadsPassportFacebookMigrations;
use Stratedge\PassportFacebook\Traits\PassportServiceProvider\RegistersUserRepository;

class PassportServiceProvider extends BasePassportServiceProvider
{
    use EnablesFacebookGrant,
        LoadsPassportFacebookMigrations,
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

        //Need the Authorization to be fully registered first...
        $this->enableFacebookGrant();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerUserRepository();

        parent::register();
    }
}
