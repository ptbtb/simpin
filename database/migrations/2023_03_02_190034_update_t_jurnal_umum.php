<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTJurnalUmum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_jurnal_umum', function (Blueprint $table) {
            $table->integer('kode_anggota')->nullable()->after('approved_by');
            $table->integer('import')->default(0)->after('kode_anggota');
            $table->string('no_ju_import', 20)->nullable()->after('import');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_jurnal_umum', function (Blueprint $table) {
            $table->dropColumn('kode_anggota');
            $table->dropColumn('import');
            $table->dropColumn('no_ju_import');
        });
    }
}
