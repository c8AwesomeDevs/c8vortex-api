<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('domain')->nullable();
            $table->string('country')->nullable();
            $table->string('industry')->nullable();
            $table->string('hear_aboutus')->nullable();
            $table->boolean('updated')->default(0);
            $table->integer('max_root')->default(0);
            $table->integer('max_sub')->default(0);
            $table->integer('max_tfmr')->default(0);
            $table->integer('max_datapoints')->default(0);
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
        Schema::dropIfExists('companies');
    }
}
