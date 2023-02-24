<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTJenisAnggotaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_jenis_anggota', function (Blueprint $table) {
            $table->integer('id_jenis_anggota')->autoIncrement();
            $table->string('code_jenis_anggota', 45);
            $table->string('nama_jenis_anggota', 45);
            $table->string('prefix', 45);
            $table->integer('create_by')->default(0);
            $table->integer('update_by')->default(0);
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
        Schema::dropIfExists('t_jenis_anggota');
    }
}
