<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenisIdToBentukPelanggarans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bentuk_pelanggarans', function (Blueprint $table) {
            $table->string('jenis_pelanggaran')->after('image')->nullable();
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
            $table->dropColumn('jenis_pelanggaran');
        });
    }
}
