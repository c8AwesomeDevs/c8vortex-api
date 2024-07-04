<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransformersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transformers', function (Blueprint $table) {
            $table->id();
            $table->integer('element_id');
            $table->date('startup_date')->nullable();
            $table->string('manufacturer', 100)->nullable();
            $table->string('type', 100)->nullable();
            $table->string('construction_year')->nullable();
            $table->string('age_band')->nullable();
            $table->string('line_capacity')->nullable();
            $table->string('winding_voltage')->nullable();
            $table->string('asset_desc', 225)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('country_manufacturer')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('model_no')->nullable();
            $table->string('volt_capacity')->nullable();
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
        Schema::dropIfExists('transformers');
    }
}
