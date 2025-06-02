<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Adiciona chave estrangeira na tabela 'supplier' (usando nome singular conforme primeira migração)
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'address_id')) {
                $table->foreignId('address_id')->constrained('address')->onDelete('cascade');
            }
        });

        // Adiciona chave estrangeira na tabela 'product' (usando nome singular conforme primeira migração)
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('cascade');
            }
        });

        // Adiciona chave estrangeira na tabela 'customer'
        Schema::table('customer', function (Blueprint $table) {
            if (!Schema::hasColumn('customer', 'address_id')) {
                $table->foreignId('address_id')->constrained('address')->onDelete('cascade');
            }
        });

        // Adiciona chave estrangeira na tabela 'sales'
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'customer_id')) {
                $table->foreignId('customer_id')->constrained('customer')->onDelete('cascade');
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'expense_types_id')) {
                $table->foreignId('expense_types_id')->constrained('expense_types')->onDelete('cascade');
            }
        });

        // Adiciona chave estrangeira para categories na tabela 'incomes' (da primeira migração)
        Schema::table('incomes', function (Blueprint $table) {
            if (!Schema::hasColumn('incomes', 'categories_id')) {
                $table->foreignId('categories_id')->constrained('categories')->onDelete('cascade');
            }
        });

        // Adiciona chaves estrangeiras na tabela 'product_sale' (nome singular conforme primeira migração)
        Schema::table('products_sales', function (Blueprint $table) {
            if (!Schema::hasColumn('products_sales', 'product_id')) {
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            }
            if (!Schema::hasColumn('products_sales', 'sale_id')) {
                $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            }
        });

        // Adiciona chave estrangeira na tabela 'users'
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        // Remove as chaves estrangeiras e colunas adicionadas
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropColumn('address_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });

        Schema::table('customer', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropColumn('address_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['expense_types_id']);
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->dropForeign(['categories_id']);
            $table->dropColumn('categories_id');
        });

        Schema::table('products_sales', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['sale_id']);
            $table->dropColumn(['product_id', 'sale_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};