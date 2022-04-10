<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SumberDanaMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_sumber_dana', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::table('t_code', function (Blueprint $table) {
            $table->integer('sumber_dana_id')->nullable()->after('code_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_code', function (Blueprint $table) {
            $table->dropColumn('sumber_dana_id');
        });

        Schema::drop('t_sumber_dana');
    }
}
