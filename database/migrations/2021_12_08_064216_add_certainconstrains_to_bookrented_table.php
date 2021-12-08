<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCertainconstrainsToBookrentedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('books_user_renteds', function (Blueprint $table) {
             $table->date('returned_date')->nullable()->change();
             $table->integer('total_days')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books_user_renteds', function (Blueprint $table) {
            //
        });
    }
}
