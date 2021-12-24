<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditTPengajuan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_pengajuan', function (Blueprint $table)
        {
            $table->float('transfer_simpanan_pagu', 28,2)->nullable()->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_pengajuan', function (Blueprint $table)
        {
            $table->dropColumn('transfer_simpanan_pagu');
        });
    }
}
