<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTindakLanjutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tindak_lanjuts', function (Blueprint $table) {
            $table->id();
            $table->string('laporan_id');
            $table->string('updated_by');
            $table->string('keterangan');
            $table->integer('status')->comment('0=diterima,1=ditindak lanjut,2=selesai');
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
        Schema::dropIfExists('tindak_lanjuts');
    }
}
