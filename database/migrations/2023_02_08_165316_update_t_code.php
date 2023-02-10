<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_code', function (Blueprint $table) {
            $table->string('induk_id', 12)->nullable()->after('sumber_dana_id');
            $table->integer('active')->default(1)->after('u_entry');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_jurnal', function (Blueprint $table) {
            $table->dropColumn('induk_id');
            $table->dropColumn('active');
        });
    }
}
