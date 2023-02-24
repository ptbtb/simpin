<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTSimpanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_simpan', function (Blueprint $table) {
            $table->integer('kode_simpan')->autoIncrement();
            $table->string('jenis_simpan', 20);
            $table->double('besar_simpanan');
            $table->double('temp_besar_simpanan')->default(0);
            $table->integer('kode_anggota');
            $table->integer('serial_number')->nullable();
            $table->string('u_entry', 50);
            $table->date('periode')->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_entri');
            $table->string('kode_jenis_simpan', 50)->nullable();
            $table->integer('id_akun_debet')->nullable();
            $table->tinyInteger('id_status_simpanan')->nullable()->default(1);
            $table->string('keterangan', 190)->nullable();
            $table->integer('mutasi')->default(0);
            $table->integer('updated_by')->nullable();
            $table->date('tgl_transaksi')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_simpan');
    }
}
