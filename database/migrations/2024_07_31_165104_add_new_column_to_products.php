<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Check if the column exists before adding it
            if (!Schema::hasColumn('products', 'todays_deal')) {
                $table->integer('todays_deal')->default(0);
            }
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku');
            }
            if (!Schema::hasColumn('products', 'unit')) {
                $table->string('unit')->nullable();
            }
            if (!Schema::hasColumn('products', 'min_qty')) {
                $table->integer('min_qty')->default(0);
            }
            if (!Schema::hasColumn('products', 'tax')) {
                $table->double('tax', 8, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'tax_type')) {
                $table->string('tax_type')->nullable();
            }
            if (!Schema::hasColumn('products', 'shipping_type')) {
                $table->string('shipping_type')->nullable();
            }
            if (!Schema::hasColumn('products', 'shipping_cost')) {
                $table->double('shipping_cost', 8, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'meta_title')) {
                $table->mediumText('meta_title')->nullable();
            }
            if (!Schema::hasColumn('products', 'meta_description')) {
                $table->longText('meta_description')->nullable();
            }
            if (!Schema::hasColumn('products', 'pdf')) {
                $table->string('pdf')->nullable();
            }
            if (!Schema::hasColumn('products', 'rating')) {
                $table->double('rating', 8, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'purchase_price')) {
                $table->double('purchase_price', 8, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'tags')) {
                $table->mediumText('tags')->nullable();
            }
            if (!Schema::hasColumn('products', 'video_link')) {
                $table->string('video_link')->nullable();
            }
            if (!Schema::hasColumn('products', 'sub_child_cat_id')) {
                $table->unsignedBigInteger('sub_child_cat_id')->nullable();
            }
            if (!Schema::hasColumn('products', 'video_provider_id')) {
                $table->unsignedBigInteger('video_provider_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the columns if they exist
            if (Schema::hasColumn('products', 'todays_deal')) {
                $table->dropColumn('todays_deal');
            }
            if (Schema::hasColumn('products', 'sku')) {
                $table->dropColumn('sku');
            }
            if (Schema::hasColumn('products', 'unit')) {
                $table->dropColumn('unit');
            }
            if (Schema::hasColumn('products', 'min_qty')) {
                $table->dropColumn('min_qty');
            }
            if (Schema::hasColumn('products', 'tax')) {
                $table->dropColumn('tax');
            }
            if (Schema::hasColumn('products', 'tax_type')) {
                $table->dropColumn('tax_type');
            }
            if (Schema::hasColumn('products', 'shipping_type')) {
                $table->dropColumn('shipping_type');
            }
            if (Schema::hasColumn('products', 'shipping_cost')) {
                $table->dropColumn('shipping_cost');
            }
            if (Schema::hasColumn('products', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
            if (Schema::hasColumn('products', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
            if (Schema::hasColumn('products', 'pdf')) {
                $table->dropColumn('pdf');
            }
            if (Schema::hasColumn('products', 'rating')) {
                $table->dropColumn('rating');
            }
            if (Schema::hasColumn('products', 'purchase_price')) {
                $table->dropColumn('purchase_price');
            }
            if (Schema::hasColumn('products', 'tags')) {
                $table->dropColumn('tags');
            }
            if (Schema::hasColumn('products', 'video_link')) {
                $table->dropColumn('video_link');
            }
            if (Schema::hasColumn('products', 'sub_child_cat_id')) {
                $table->dropColumn('sub_child_cat_id');
            }
            if (Schema::hasColumn('products', 'video_provider_id')) {
                $table->dropColumn('video_provider_id');
            }
        });
    }
}
