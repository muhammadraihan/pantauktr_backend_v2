<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFcmRegistrationTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fcm_registration_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('pelapor_id');
            $table->string('token')->nullable();
            $table->boolean('revoked')->nullable();
            $table->timestamps();
            $table->dateTime('expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fcm_registration_tokens');
    }
}
