<?php

namespace Stratedge\PassportFacebook\Traits\PassportServiceProvider;

trait LoadsPassportFacebookMigrations
{
    protected function loadPassportFacebookMigrations()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}
