<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePelaporsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pelapors', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('provider')->nullable();
            $table->string('avatar')->nullable();
            $table->bigInteger('reward_point')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->string('last_login_at')->nullable();
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
        Schema::dropIfExists('pelapors');
    }
}
