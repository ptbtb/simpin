<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTJenisPinjamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_jenis_pinjam', function (Blueprint $table) {
            $table->string('kode_jenis_pinjam', 11);
            $table->integer('tipe_jenis_pinjaman_id')->nullable();
            $table->integer('kategori_jenis_pinjaman_id')->nullable();
            $table->string('nama_pinjaman', 50);
            $table->integer('lama_angsuran');
            $table->double('maks_pinjam');
            $table->float('bunga');
            $table->float('asuransi')->default(0);
            $table->float('biaya_admin')->default(100000);
            $table->float('provisi')->default(0.01);
            $table->float('jasa')->default(0.02);
            $table->float('jasa_pelunasan_dipercepat')->default(0);
            $table->integer('minimal_angsur_pelunasan')->nullable();
            $table->string('u_entry', 50);
            $table->date('tgl_entri');
            $table->tinyInteger('can_change_tenor')->default(0);
            $table->float('jasa_topup')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_jenis_pinjam');
    }
}
