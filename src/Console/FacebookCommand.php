<?php

namespace Laravel\Passport\Console;

use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;

class ClientCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passport:facebook {client_id : ID of the client}';

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
    public function handle(ClientRepository $clients)
    {
        $client = Client::find($this->argument("client_id"));

        if (!$client) {
            throw new Exception("Could not find client with ID " . $this->argument("client_id"));
        }

        $client->facebook_client = true;

        $client->save();

        $this->info("Client " . $this->argument("client_id") . " has been granted access to the Facebook grant.");
    }
}
