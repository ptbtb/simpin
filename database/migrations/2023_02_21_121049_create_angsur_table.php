<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAngsurTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_angsur', function (Blueprint $table) {
            $table->integer('kode_angsur')->autoIncrement();
            $table->string('kode_pinjam', 250);
            $table->integer('angsuran_ke');
            $table->double('besar_angsuran', 28, 2)->default(0.00);
            $table->double('denda', 28, 2)->default(0.00);
            $table->double('jasa', 28, 2)->default(0.00);
            $table->double('diskon', 28, 2)->default(0.00);
            $table->double('sisa_pinjam', 28, 2)->default(0.00);
            $table->integer('kode_anggota')->nullable();
            $table->integer('id_akun_kredit')->nullable();
            $table->integer('serial_number')->nullable();
            $table->string('u_entry', 50);
            $table->date('tgl_entri');
            $table->date('tgl_transaksi')->nullable();
            $table->date('temp_tgl_transaksi')->nullable();
            $table->date('jatuh_tempo')->nullable();
            $table->integer('id_status_angsuran')->default(1);
            $table->double('besar_pembayaran')->default(0);
            $table->double('besar_pembayaran_jasa')->default(0);
            $table->double('temp_besar_pembayaran')->default(0);
            $table->dateTime('paid_at')->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('real_payment')->nullable();
            $table->integer('deleted_by')->nullable();
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
        Schema::dropIfExists('t_angsur');
    }
}
