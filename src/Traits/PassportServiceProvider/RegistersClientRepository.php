<?php

namespace Stratedge\PassportFacebook\Traits\PassportServiceProvider;

use Laravel\Passport\Bridge\ClientRepository as BaseClientRepository;
use Stratedge\PassportFacebook\Bridge\ClientRepository;

trait RegistersClientRepository
{
    protected function registerClientRepository()
    {
        $this->app->bind(BaseClientRepository::class, function ($app) {
            return $app->make(ClientRepository::class);
        });
    }
}
