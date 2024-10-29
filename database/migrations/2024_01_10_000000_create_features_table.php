<?php

// database/migrations/2024_01_10_000000_create_features_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeaturesTable extends Migration
{
    public function up()
    {
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes(); // Optional: If you want to implement soft deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('features');
    }
}