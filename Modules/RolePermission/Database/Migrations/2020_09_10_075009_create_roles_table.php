<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('details')->nullable();
            $table->timestamps();
        });


        DB::table('roles')->insert([
            [
                'name' => 'Super admin',
                'type' => 'system_user',
                'created_at' => date('Y-m-d h:i:s')
            ],
            [
                'name' => 'Admin',
                'type' => 'system_user',
                'created_at' => date('Y-m-d h:i:s')
            ],
            [
                'name' => 'Staff',
                'type' => 'regular_user',
                'created_at' => date('Y-m-d h:i:s')
            ],
            [
                'name' => 'Supplier',
                'type' => 'normal_user',
                'created_at' => date('Y-m-d h:i:s')
            ],
            [
                'name' => 'Customer',
                'type' => 'normal_user',
                'created_at' => date('Y-m-d h:i:s')
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
