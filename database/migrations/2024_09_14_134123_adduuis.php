<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Adduuis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // check if uuid column exists
        if (!Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->uuid('uuid')->after('id')->nullable();
            });
        }

        // Add a new column to store the UUID
        DB::table('users')->whereNull('uuid')->get()->each(function ($user) {
            DB::table('users')->where('id', $user->id)->update([
                'uuid' => Str::uuid(),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Optionally remove the UUIDs (if desired)
        DB::table('users')->update(['uuid' => null]);
    }
}
