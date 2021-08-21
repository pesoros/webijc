<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLztokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lztokens', function (Blueprint $table) {
            $table->id();
            $table->string('akun_name');
            $table->string('token');
            $table->string('refresh_token');
            $table->string('token_expires_in');
            $table->string('refresh_token_expires_in');
            $table->string('api_key');
            $table->string('api_secret');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lztokens');
    }
}
