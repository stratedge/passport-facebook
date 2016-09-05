<?php

namespace Stratedge\PassportFacebook\Bridge;

use Laravel\Passport\Bridge\ClientRepository as BaseClientRepository;

class ClientRepository extends BaseClientRepository
{
    /**
     * {inheritDoc}
     */
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
}
