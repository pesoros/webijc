<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypeOpeningBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('type_opening_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id')->unsigned()->nullable();
            $table->string('type', 255)->nullable();
            $table->double('amount', 16,2)->default(0);
            $table->boolean('is_default')->default(false);
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
        Schema::dropIfExists('type_opening_balances');
    }
}
