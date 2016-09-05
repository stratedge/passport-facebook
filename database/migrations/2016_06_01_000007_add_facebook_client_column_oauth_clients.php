<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFacebookClientColumnOauthClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("oauth_clients", function (Blueprint $table) {
            $table->boolean("facebook_client")->default(0)->after("password_client");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("oauth_clients", function (Blueprint $table) {
            $table->dropColumn("facebook_client");
        });
    }
}
