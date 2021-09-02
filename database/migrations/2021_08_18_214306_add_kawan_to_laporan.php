<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKawanToLaporan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laporans', function (Blueprint $table){
            $table->string('bentuk_pelanggaran')->nullable()->after('jenis_pelanggaran');
            $table->string('bentuk_apresiasi')->nullable()->after('bentuk_pelanggaran');
            $table->string('kawasan')->nullable()->after('bentuk_apresiasi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laporans');
    }
}
