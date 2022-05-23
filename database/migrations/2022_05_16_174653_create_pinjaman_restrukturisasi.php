<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinjamanRestrukturisasi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pinjaman_restrukturisasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pinjam');
            $table->string('old_tenor');
            $table->string('old_angsuran');
            $table->string('new_tenor');
            $table->string('new_angsuran');
            $table->string('dokumen_persetujuan');
            $table->integer('created_by');
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
        Schema::dropIfExists('pinjaman_restrukturisasi');
    }
}
