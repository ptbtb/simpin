<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnggotaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_anggota', function (Blueprint $table) {
            $table->integer('kode_anggota');
            $table->integer('company_id')->nullable();
            $table->integer('kelas_company_id')->nullable();
            $table->integer('kode_tabungan')->nullable();
            $table->integer('id_jenis_anggota')->nullable();
            $table->string('nama_anggota', 50);
            $table->string('alamat_anggota', 100)->nullable();
            $table->string('jenis_kelamin', 10)->nullable();
            $table->string('lokasi_kerja', 50)->nullable();
            $table->date('tgl_masuk')->nullable();
            $table->string('telp', 16)->nullable();
            $table->string('tempat_lahir', 16)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('status', 10)->nullable();
            $table->string('u_entry', 50);
            $table->string('no_rek', 45)->nullable();
            $table->string('nipp', 45)->nullable();
            $table->string('ktp', 50)->nullable();
            $table->string('foto_ktp', 190)->nullable();
            $table->string('email', 190)->nullable();
            $table->string('emergency_kontak', 45)->nullable();
            $table->integer('id_bank')->nullable();
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
        Schema::dropIfExists('t_anggota');
    }
}
