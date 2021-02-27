<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableEcProductCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_product_categories', function (Blueprint $table) {
            $table->integer('parent_id')->unsigned()->default(0)->change();
        });

        DB::table('ec_product_categories')->whereNull('parent_id')->update(['parent_id' => 0]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec_product_categories', function (Blueprint $table) {
            $table->integer('parent_id')->nullable()->change();
        });
    }
}
