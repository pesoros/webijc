<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductItemDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_item_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('itemable_id')->nullable();
            $table->string('itemable_type', 255)->nullable();
            $table->unsignedInteger('productable_id')->nullable();
            $table->string('productable_type', 255)->nullable();
            $table->unsignedInteger('product_sku_id');
            $table->double('price', 16,2)->default(0);
            $table->integer('quantity')->default(0);
            $table->double('tax', 16,2)->default(0);
            $table->double('discount', 16,2)->default(0);
            $table->double('sub_total', 16,2)->default(0);
            $table->integer('return_quantity')->default(0);
            $table->double('return_amount', 16,2)->default(0);
            $table->timestamp('return_date');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('product_item_details');
    }
}
