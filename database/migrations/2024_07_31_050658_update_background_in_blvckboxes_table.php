<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBackgroundInBlvckboxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blvckboxes', function (Blueprint $table) {
            $table->string('background')->nullable()->default('storage/blvckbox/blvckbox_dummy_bg.jpg')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blvckboxes', function (Blueprint $table) {
            $table->string('background')->default('storage/blvckbox/blvckbox_dummy_bg.jpg')->change();
        });
    }
}
