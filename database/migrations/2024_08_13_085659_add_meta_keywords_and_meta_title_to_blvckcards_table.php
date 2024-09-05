<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetaKeywordsAndMetaTitleToBlvckcardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blvckcards', function (Blueprint $table) {
            $table->string('meta_keywords')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blvckcards', function (Blueprint $table) {
            $table->dropColumn(['meta_keywords']);
        });
    }
}
