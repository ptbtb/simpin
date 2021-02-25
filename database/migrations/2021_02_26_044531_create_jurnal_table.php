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
            $table->string('jenis');
            $table->unsignedBigInteger('nomer');
            $table->string('akun_kredit');
            $table->float('kredit');
            $table->string('akun_debet');
            $table->float('debet');
            $table->string('Keterangan');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            
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
