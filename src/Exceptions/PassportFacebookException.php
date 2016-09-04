<?php

namespace Stratedge\PassportFacebook\Exceptions;

use League\OAuth2\Server\Exception\OAuthServerException;

class PassportFacebookException extends OAuthServerException
{
    public static function missingEmailScope()
    {
        return new static(
            "The provided Facebook token does not grant the email scope.",
            10,
            "invalid_credentials"
        );
    }

    public static function saveUserFailure()
    {
        return new static(
            "Failed to save the new user.",
            11,
            "server_error"
        );
    }
}
