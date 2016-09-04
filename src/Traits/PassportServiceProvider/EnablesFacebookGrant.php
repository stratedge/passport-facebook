<?php

namespace Stratedge\PassportFacebook\Traits\PassportServiceProvider;

use Stratedge\PassportFacebook\Grants\FacebookGrant;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;

trait EnablesFacebookGrant
{
    protected function enableFacebookGrant()
    {
        $this->app->make(AuthorizationServer::class)->enableGrantType(
            $this->makeFacebookGrant(),
            Passport::tokensExpireIn()
        );
    }

    protected function makeFacebookGrant()
    {
        $grant = new FacebookGrant(
            $this->app->make(\Laravel\Passport\Bridge\UserRepository::class),
            $this->app->make(\Laravel\Passport\Bridge\RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
