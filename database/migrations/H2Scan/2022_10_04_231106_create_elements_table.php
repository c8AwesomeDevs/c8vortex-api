<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elements', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('parent_id')->nullable();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->string('path', 255)->nullable();
            $table->string('has_child', 255)->nullable();
            $table->boolean('template_created')->default(0);
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
        Schema::dropIfExists('elements');
    }
}
