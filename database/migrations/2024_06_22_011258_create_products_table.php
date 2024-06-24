<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('entity_id')->primary();
            $table->string('sku')->unique();
            $table->text('CategoryName')->nullable();
            $table->text('name');
            $table->text('description')->nullable();
            $table->text('shortdesc')->nullable();
            $table->text('price')->nullable();
            $table->text('link')->nullable();
            $table->text('image')->nullable();
            $table->text('Brand')->nullable();
            $table->text('Rating')->nullable();
            $table->text('CaffeineType')->nullable();
            $table->text('Count')->nullable();
            $table->text('Flavored')->nullable();
            $table->text('Seasonal')->nullable();
            $table->text('Instock')->nullable();
            $table->text('Facebook')->nullable();
            $table->text('IsKCup')->nullable();
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
        Schema::dropIfExists('products');
    }
};
