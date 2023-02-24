<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurnalTempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurnal_temp', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->nullable();
            $table->string('kode_anggota', 50)->nullable();
            $table->string('nama', 190)->default('');
            $table->string('nip', 50)->nullable();
            $table->integer('normal_balance')->nullable();
            $table->double('jumlah')->nullable();
            $table->date('tgl_posting')->nullable();
            $table->integer('no_bukti')->nullable();
            $table->string('kd_bukti', 50)->nullable();
            $table->text('uraian_1')->nullable();
            $table->text('uraian_2')->nullable();
            $table->text('uraian_3')->nullable();
            $table->tinyInteger('is_success')->default(0);
            $table->string('keterangan_gagal', 190)->nullable();
            $table->string('unik_bukti', 190)->nullable();
            $table->string('DK', 45)->nullable();
            $table->string('unit', 50)->nullable();
            $table->text('flag')->nullable();
            $table->integer('serial_number')->nullable();
            $table->date('periode')->nullable();
            $table->integer('sync2')->default(1);
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
        Schema::dropIfExists('jurnal_temp');
    }
}
