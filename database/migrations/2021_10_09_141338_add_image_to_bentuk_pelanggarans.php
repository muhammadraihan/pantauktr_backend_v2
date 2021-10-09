<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageToBentukPelanggarans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bentuk_pelanggarans', function (Blueprint $table) {
            $table->string('image')->after('keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bentuk_pelanggarans', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}
