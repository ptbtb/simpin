<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTJenisSimpanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_jenis_simpan', function (Blueprint $table) {
            $table->string('kode_jenis_simpan', 11);
            $table->string('nama_simpanan', 50);
            $table->float('besar_simpanan');
            $table->string('u_entry', 50);
            $table->date('tgl_entri');
            $table->integer('tgl_tagih')->nullable();
            $table->integer('hari_jatuh_tempo')->nullable();
            $table->integer('is_required')->default(0);
            $table->integer('sequence')->default(0);
            $table->integer('is_normal_withdraw')->default(0);
            $table->float('max_withdraw')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_jenis_simpan');
    }
}
