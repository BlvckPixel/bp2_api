<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlvckcardImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blvckcard_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blvckcard_id');
            $table->foreign('blvckcard_id')->references('id')->on('blvckcards')->onDelete('cascade');
            $table->string('image_path');
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
        Schema::dropIfExists('blvckcard_images');
    }
}
