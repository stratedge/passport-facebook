<?php

namespace Stratedge\PassportFacebook\Traits\PassportServiceProvider;

use Stratedge\PassportFacebook\Console\FacebookCommand;

trait RegistersFacebookCommand
{
    protected function registerFacebookCommand()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FacebookCommand::class,
            ]);
        }
    }
}
