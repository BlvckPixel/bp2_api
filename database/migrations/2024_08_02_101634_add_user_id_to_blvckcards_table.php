<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUserIdToBlvckcardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blvckcards', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('contentcard_id');
        });

        DB::table('blvckcards')->whereNotIn('user_id', function ($query) {
            $query->select('id')->from('users');
        })->update(['user_id' => NULL]);

        Schema::table('blvckcards', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
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
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

    }
}
