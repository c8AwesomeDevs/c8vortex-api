<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDashboardConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dashboard_configurations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('asset_id');
            $table->integer('active_symbol');
            $table->integer('timerange');
            $table->boolean('realtime');
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
        Schema::dropIfExists('dashboard_configurations');
    }
}
