<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('company_id');
            $table->string('subscription_type', 10);
            $table->decimal('subtotal');
            $table->decimal('total');
            $table->string('currency', 10);
            $table->string('payment_status');
            $table->date('activation_date');
            $table->date('expiration_date');
            $table->boolean('activated')->default(0);
            $table->string('reference_id', 255);
            $table->string('stripe_subscription_id', 255)->nullable();
            $table->string('stripe_customer_id', 255)->nullable();
            $table->string('stripe_subscription_item_id', 255)->nullable();
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
        Schema::dropIfExists('subscriptions');
    }
}
