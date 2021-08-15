<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBentukApresiasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bentuk_apresiasis', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('bentuk_apresiasi');
            $table->string('created_by')->nullable();
            $table->string('edited_by')->nullable();
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
        Schema::dropIfExists('bentuk_apresiasis');
    }
}
