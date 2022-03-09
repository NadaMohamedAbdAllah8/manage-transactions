<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');

            $table->unsignedBigInteger('sub_category_id')->nullable(TRUE);
            $table->foreign('sub_category_id')->references('id')->on('sub_categories');

            $table->double('amount');

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');

            $table->timestamp('due_date');

            $table->integer('VAT');

            $table->boolean('is_VAT_inclusive');

            $table->unsignedBigInteger('status_id');
            $table->foreign('status_id')->references('id')->on('transaction_statuses');

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
        Schema::dropIfExists('transactions');
    }
}