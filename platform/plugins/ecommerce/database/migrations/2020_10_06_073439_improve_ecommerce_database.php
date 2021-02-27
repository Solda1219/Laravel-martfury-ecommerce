<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImproveEcommerceDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('ec_brands', 'user_id')) {
            Schema::table('ec_brands', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasColumn('ec_product_categories', 'user_id')) {
            Schema::table('ec_product_categories', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasColumn('ec_products', 'user_id')) {
            Schema::table('ec_products', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasColumn('ec_product_attribute_sets', 'created_by')) {
            Schema::table('ec_product_attribute_sets', function (Blueprint $table) {
                $table->dropColumn(['created_by', 'updated_by']);
            });
        }

        if (Schema::hasColumn('ec_product_attributes', 'created_by')) {
            Schema::table('ec_product_attributes', function (Blueprint $table) {
                $table->dropColumn(['created_by', 'updated_by']);
            });
        }

        if (Schema::hasColumn('ec_reviews', 'user_id')) {
            Schema::table('ec_reviews', function (Blueprint $table) {
                $table->renameColumn('user_id', 'customer_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('ec_brands', 'user_id')) {
            Schema::table('ec_brands', function (Blueprint $table) {
                $table->integer('user_id')->nullable();
            });
        }

        if (!Schema::hasColumn('ec_product_categories', 'user_id')) {
            Schema::table('ec_product_categories', function (Blueprint $table) {
                $table->integer('user_id')->nullable();
            });
        }

        if (!Schema::hasColumn('ec_products', 'user_id')) {
            Schema::table('ec_products', function (Blueprint $table) {
                $table->integer('user_id')->nullable();
            });
        }

        if (!Schema::hasColumn('ec_product_attribute_sets', 'created_by')) {
            Schema::table('ec_product_attribute_sets', function (Blueprint $table) {
                $table->integer('created_by')->unsigned()->nullable();
                $table->integer('updated_by')->unsigned()->nullable();
            });
        }

        if (!Schema::hasColumn('ec_product_attributes', 'created_by')) {
            Schema::table('ec_product_attributes', function (Blueprint $table) {
                $table->integer('created_by')->unsigned()->nullable();
                $table->integer('updated_by')->unsigned()->nullable();
            });
        }

        if (!Schema::hasColumn('ec_reviews', 'customer_id')) {
            Schema::table('ec_reviews', function (Blueprint $table) {
                $table->renameColumn('customer_id', 'user_id');
            });
        }
    }
}
