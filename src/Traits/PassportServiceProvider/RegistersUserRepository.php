<?php

namespace Stratedge\PassportFacebook\Traits\PassportServiceProvider;

use Laravel\Passport\Bridge\UserRepository as BaseUserRepository;
use Stratedge\PassportFacebook\Bridge\UserRepository;

trait RegistersUserRepository
{
    protected function registerUserRepository()
    {
        $this->app->bind(BaseUserRepository::class, function ($app) {
            return $app->make(UserRepository::class);
        });
    }
}
