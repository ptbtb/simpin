<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJkkSequenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jkk_sequence', function (Blueprint $table) {
            $table->bigInteger('next_not_cached_value');
            $table->bigInteger('minimum_value');
            $table->bigInteger('maximum_value');
            $table->bigInteger('start_value')->comment('start value when sequences is created or value if RESTART is used');
            $table->bigInteger('increment')->comment('increment value');
            $table->unsignedBigInteger('cache_size');
            $table->unsignedTinyInteger('cycle_option')->comment('0 if no cycles are allowed, 1 if the sequence should begin a new cycle when maximum_value is passed');
            $table->bigInteger('cycle_count')->comment('How many cycles have been done');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jkk_sequence');
    }
}
