<?php

namespace Stratedge\PassportFacebook\Traits\UserRepository;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphUser;
use RuntimeException;
use Stratedge\PassportFacebook\Exceptions\PassportFacebookException;

trait GetsUserByFacebookToken
{
    protected $facebook_fields = ["id", "name", "email"];

    public function getUserEntityByFacebookToken($token)
    {
        $fb = app(Facebook::class);

        try {
            $endpoint = "/me?fields=" . implode(",", $this->facebook_fields);
            $response = $fb->get($endpoint, $token);
        } catch (FacebookSDKException $e) {
            return;
        }

        $fb_user = $response->getGraphUser();

        $user = $this->getUserEntityByFacebookId($fb_user->getId());

        if (!$user) {
            $user = $this->createUserEntityFromFacebookUser($fb_user);
        }

        return $user;
    }

    protected function getUserEntityByFacebookId($id)
    {
        if (is_null($model = config('auth.providers.users.model'))) {
            throw new RuntimeException('Unable to determine user model from configuration.');
        }

        $user = (new $model)->where("facebook_id", $id)->first();

        if (!$user) {
            return;
        }

        return new User($user->id);
    }

    protected function createUserEntityFromFacebookUser($fb_user)
    {
        if (is_null($model = config('auth.providers.users.model'))) {
            throw new RuntimeException('Unable to determine user model from configuration.');
        }

        $user = new $model;

        $user->name = $fb_user->getName();

        if (!$fb_user->getEmail()) {
            throw PassportFacebookException::missingEmailScope();
        }

        $user->email = $fb_user->getEmail();

        $user->facebook_id = $fb_user->getId();

        if (!$user->save()) {
            throw PassportFacebookException::saveUserFailure();
        }

        return $user;
    }
}
