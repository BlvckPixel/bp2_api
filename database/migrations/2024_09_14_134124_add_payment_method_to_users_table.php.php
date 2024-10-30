<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentMethodToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('payment_method', ['card', 'crypto', 'note'])->default('card')->after('email');
            $table->string('stripe_customer_id')->nullable()->after('payment_method');
            $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'stripe_customer_id', 'stripe_subscription_id']);
        });
    }
}
