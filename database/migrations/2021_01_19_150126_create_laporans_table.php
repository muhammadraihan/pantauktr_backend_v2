<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('jenis_pelanggaran')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('photo')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('nama_lokasi')->nullable();
            $table->string('alamat')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kota')->nullable();
            $table->string('propinsi')->nullable();
            $table->string('negara')->nullable();
            $table->string('place_id')->nullable();
            $table->string('jenis_laporan')->nullable();
            $table->string('jenis_apresiasi')->nullable();
            $table->string('creates_by')->nullable();
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
        Schema::dropIfExists('laporans');
    }
}
