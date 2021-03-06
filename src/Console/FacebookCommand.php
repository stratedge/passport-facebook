<?php

namespace Stratedge\PassportFacebook\Console;

use Illuminate\Console\Command;
use Laravel\Passport\Client;

class FacebookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passport:facebook
        {client_id : ID of the client}
        {--r|revoke : Revoke access to the Facebook grant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant a client access to the Facebook grant';

    /**
     * Execute the console command.
     *
     * @param  \Laravel\Passport\ClientRepository  $clients
     * @return void
     */
    public function handle()
    {
        $client = Client::find($this->argument("client_id"));

        if (!$client) {
            throw new Exception("Could not find client with ID " . $this->argument("client_id"));
        }

        if ($this->option("revoke")) {
            $this->revokeAccessToFacebookGrant($client);
        } else {
            $this->giveAccessToFacebookGrant($client);
        }
    }

    protected function giveAccessToFacebookGrant(Client $client)
    {
        $client->facebook_client = true;
        $client->save();
        $this->info("Client " . $this->argument("client_id") . " has been granted access to the Facebook grant.");
    }

    protected function revokeAccessToFacebookGrant(Client $client)
    {
        $client->facebook_client = false;
        $client->save();

        $this->info("Client " . $this->argument("client_id") . " has had access to the Facebook grant revoked.");
    }
}
