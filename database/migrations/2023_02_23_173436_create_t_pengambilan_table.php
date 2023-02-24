<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTPengambilanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_pengambilan', function (Blueprint $table) {
            $table->integer('kode_ambil')->autoIncrement();
            $table->integer('kode_anggota')->nullable();
            $table->string('id_tabungan', 250)->nullable();
            $table->integer('kode_tabungan');
            $table->integer('besar_ambil');
            $table->date('tgl_ambil');
            $table->string('keterangan', 200)->nullable();
            $table->string('code_trans', 50)->nullable();
            $table->integer('id_akun_debet')->nullable();
            $table->integer('serial_number')->nullable();
            $table->string('u_entry', 50)->nullable();
            $table->integer('status_pengambilan')->nullable();
            $table->dateTime('tgl_acc')->nullable();
            $table->string('no_jkk', 250)->nullable();
            $table->tinyInteger('status_jkk')->nullable()->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('approved_by')->nullable();
            $table->integer('paid_by_cashier')->nullable();
            $table->string('bukti_pembayaran', 250)->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('is_exit_anggota')->nullable()->default(0);
            $table->tinyInteger('is_pelunasan_dipercepat')->nullable()->default(0);
            $table->date('tgl_transaksi')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('is_simpanan_to_simpanan')->nullable();
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
        Schema::dropIfExists('t_pengambilan');
    }
}
