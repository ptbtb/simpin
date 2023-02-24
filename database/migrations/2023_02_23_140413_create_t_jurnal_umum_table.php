<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTJurnalUmumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_jurnal_umum', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_transaksi')->nullable();
            $table->integer('serial_number')->nullable();
            $table->string('deskripsi', 190);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('status_jurnal_umum_id')->nullable();
            $table->date('tgl_acc')->nullable();
            $table->integer('paid_by_cashier')->nullable();
            $table->integer('approved_by')->nullable();
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
        Schema::dropIfExists('t_jurnal_umum');
    }
}
