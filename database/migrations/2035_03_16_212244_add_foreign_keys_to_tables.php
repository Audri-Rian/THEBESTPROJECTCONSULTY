<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'address_id')) {
                $table->foreignId('address_id')->constrained('address')->onDelete('cascade');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('cascade');
            }
        });

        Schema::table('customer', function (Blueprint $table) {
            if (!Schema::hasColumn('customer', 'address_id')) {
                $table->foreignId('address_id')->constrained('address')->onDelete('cascade');
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'customer_id')) {
                $table->foreignId('customer_id')->constrained('customer')->onDelete('cascade');
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'expense_type_id')) {
                $table->foreignId('expense_type_id')->constrained('expense_type')->onDelete('cascade');
            }
        });

        Schema::table('products_prices', function (Blueprint $table) {
            if (!Schema::hasColumn('products_prices', 'product_id')) {
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            }
        });

        Schema::table('products_sales', function (Blueprint $table) {
            if (!Schema::hasColumn('products_sales', 'product_id')) {
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            }
            if (!Schema::hasColumn('products_sales', 'sale_id')) {
                $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
        });

        Schema::table('customer', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['expense_type_id']);
        });

        Schema::table('products_prices', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('products_sales', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['sale_id']);
        });
    }
};
