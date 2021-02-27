<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeColumnImageInProductAttributesTableNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_product_attributes', function (Blueprint $table) {
            $table->string('color', 50)->nullable()->change();
            $table->string('image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec_product_attributes', function (Blueprint $table) {
            $table->string('color', 50)->change();
            $table->string('image')->change();
        });
    }
}
