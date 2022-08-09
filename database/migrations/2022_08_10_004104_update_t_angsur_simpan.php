<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTAngsurSimpan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_angsur_simpan', function (Blueprint $table) {
            $table->dateTime('tgl_transaksi')->nullable()->after('tgl_entri');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_angsur_simpan', function (Blueprint $table) {
            $table->dropColumn('tgl_transaksi');
        });
    }
}
