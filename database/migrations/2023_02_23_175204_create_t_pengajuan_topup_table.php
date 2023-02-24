<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTPengajuanTopupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_pengajuan_topup', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan', 250)->nullable();
            $table->string('kode_pinjaman', 250)->nullable();
            $table->integer('jasa_pelunasan_dipercepat')->nullable();
            $table->integer('total_bayar_pelunasan_dipercepat')->nullable();
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
        Schema::dropIfExists('t_pengajuan_topup');
    }
}
