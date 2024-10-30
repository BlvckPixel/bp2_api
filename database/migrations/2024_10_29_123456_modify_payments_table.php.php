<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('currency')->default('GBP')->after('amount');
            $table->string('stripe_payment_id')->nullable()->after('status');
            $table->string('payment_type')->default('subscription')->after('stripe_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['currency', 'stripe_payment_id', 'payment_type']);
        });
    }
}
