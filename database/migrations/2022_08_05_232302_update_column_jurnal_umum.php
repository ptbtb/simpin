<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnJurnalUmum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_jurnal_umum', function (Blueprint $table) {
            $table->integer('paid_by_cashier')->nullable()->default(null)->after('status_jkk');
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
            $table->dropColumn('paid_by_cashier');
        });
    }
}
