<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Adiciona chave estrangeira na tabela 'supplier' (usando nome singular conforme primeira migração)
        Schema::table('supplier', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier', 'address_id')) {
                $table->foreignId('address_id')->constrained('address')->onDelete('cascade');
            }
        });

        // Adiciona chave estrangeira na tabela 'product' (usando nome singular conforme primeira migração)
        Schema::table('product', function (Blueprint $table) {
            if (!Schema::hasColumn('product', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->constrained('supplier')->onDelete('cascade');
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

        // Adiciona chaves estrangeiras na tabela 'expenses' (incluindo categories_id da primeira migração)
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'expense_type_id')) {
                $table->foreignId('expense_type_id')->constrained('expense_types')->onDelete('cascade');
            }

            if (!Schema::hasColumn('expenses', 'categories_id')) {
                $table->foreignId('categories_id')->constrained('categories')->onDelete('cascade');
            }
        });

        // Adiciona chave estrangeira para categories na tabela 'incomes' (da primeira migração)
        Schema::table('incomes', function (Blueprint $table) {
            if (!Schema::hasColumn('incomes', 'categories_id')) {
                $table->foreignId('categories_id')->constrained('categories')->onDelete('cascade');
            }
        });

        // Adiciona chave estrangeira na tabela 'product_price' (nome singular conforme primeira migração)
        Schema::table('product_price', function (Blueprint $table) {
            if (!Schema::hasColumn('product_price', 'product_id')) {
                $table->foreignId('product_id')->constrained('product')->onDelete('cascade');
            }
        });

        // Adiciona chaves estrangeiras na tabela 'product_sale' (nome singular conforme primeira migração)
        Schema::table('product_sale', function (Blueprint $table) {
            if (!Schema::hasColumn('product_sale', 'product_id')) {
                $table->foreignId('product_id')->constrained('product')->onDelete('cascade');
            }
            if (!Schema::hasColumn('product_sale', 'sale_id')) {
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
        Schema::table('supplier', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropColumn('address_id');
        });

        Schema::table('product', function (Blueprint $table) {
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
            $table->dropForeign(['expense_type_id']);
            $table->dropForeign(['categories_id']);
            $table->dropColumn(['expense_type_id', 'categories_id']);
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->dropForeign(['categories_id']);
            $table->dropColumn('categories_id');
        });

        Schema::table('product_price', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });

        Schema::table('product_sale', function (Blueprint $table) {
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