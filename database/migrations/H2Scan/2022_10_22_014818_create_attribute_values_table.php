<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->integer('element_id');
            $table->datetime('timestamp');
            $table->decimal('acetylene');
            $table->decimal('acetylene_roc')->nullable();
            $table->decimal('ethylene');
            $table->decimal('ethylene_roc')->nullable();
            $table->decimal('methane');
            $table->decimal('methane_roc')->nullable();
            $table->decimal('ethane');
            $table->decimal('ethane_roc')->nullable();
            $table->decimal('hydrogen');
            $table->decimal('hydrogen_roc')->nullable();
            $table->decimal('oxygen');
            $table->decimal('carbon_monoxide');
            $table->decimal('carbon_dioxide');
            $table->decimal('tdcg');
            $table->string('t1');
            $table->string('t2')->nullable();
            $table->string('t3_biotemp')->nullable();
            $table->string('t3_fr')->nullable();
            $table->string('t3_midel')->nullable();
            $table->string('t3_silicon')->nullable();
            $table->string('t4');
            $table->string('t5');
            $table->string('t6')->nullable();;
            $table->string('t7')->nullable();;
            $table->string('p1');
            $table->string('p2');
            $table->string('iec_ratio');
            $table->string('dornenberg');
            $table->string('rogers_ratio');
            $table->string('carbon_ratio');
            $table->string('nei');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attribute_values');
    }
}
