<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChildrenDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('children_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            // $table->unsignedBigInteger('parent_id');
            $table->string('name');
            $table->integer('age');
            $table->string('gender');
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('booking_details')->onDelete('cascade');
            // $table->foreign('parent_id')->references('id')->on('parent_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('children_details');
    }
}
