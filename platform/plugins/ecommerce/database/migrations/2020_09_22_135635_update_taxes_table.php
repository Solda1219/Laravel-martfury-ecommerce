<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_taxes', function (Blueprint $table) {
            $table->dropColumn([
                'country',
                'state',
                'post_code',
                'city',
            ]);
        });

        Schema::table('ec_products', function (Blueprint $table) {
           $table->integer('tax_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec_taxes', function (Blueprint $table) {
            $table->string('country', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('post_code')->nullable();
        });

        Schema::table('ec_products', function (Blueprint $table) {
            $table->dropColumn('tax_id');
        });
    }
}
