<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('email_templatese', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Template name (e.g., Account Activation)
            $table->string('subject'); // Email subject
            $table->text('body'); // Email body (HTML format)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_templatese');
    }
}
