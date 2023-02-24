<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurnalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_jurnal', function (Blueprint $table) {
            $table->id();
            $table->integer('id_tipe_jurnal')->nullable();
            $table->integer('jurnalable_id')->nullable();
            $table->string('jurnalable_type', 100)->nullable();
            $table->unsignedBigInteger('nomer');
            $table->string('akun_kredit', 190);
            $table->double('kredit');
            $table->string('akun_debet', 190);
            $table->double('debet');
            $table->string('Keterangan', 190);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->date('tgl_transaksi')->nullable();
            $table->date('deleted_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->integer('deleted_by')->nullable();
            $table->string('trans_id', 190)->nullable();
            $table->integer('anggota')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jurnal');
    }
}
