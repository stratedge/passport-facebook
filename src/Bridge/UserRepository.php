<?php

namespace Stratedge\PassportFacebook\Bridge;

use Laravel\Passport\Bridge\UserRepository as BaseUserRepository;
use Stratedge\PassportFacebook\Traits\UserRepository\GetsUserByFacebookToken;

class UserRepository extends BaseUserRepository
{
    use GetsUserByFacebookToken;
}
