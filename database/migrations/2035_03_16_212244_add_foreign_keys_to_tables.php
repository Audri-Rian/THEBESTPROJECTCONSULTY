<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Adiciona chave estrangeira na tabela 'supplier'
        Schema::table('supplier', function (Blueprint $table) {
            // Verifica se a chave estrangeira já existe
            if (!Schema::hasColumn('supplier', 'address_id')) {
                $table->foreignId('address_id')->constrained('address')->onDelete('cascade');
            }
        });

        // Adiciona chave estrangeira na tabela 'product'
        Schema::table('product', function (Blueprint $table) {
            if (!Schema::hasColumn('product', 'supplier_id')) {
                $table->foreignId('supplier_id')->constrained('supplier')->onDelete('cascade');
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

        // Adiciona chave estrangeira na tabela 'expenses'
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'expense_type_id')) {
                $table->foreignId('expense_type_id')->constrained('expense_type')->onDelete('cascade');
            }
        });

        // Adiciona chave estrangeira na tabela 'product_price'
        Schema::table('product_price', function (Blueprint $table) {
            if (!Schema::hasColumn('product_price', 'product_id')) {
                $table->foreignId('product_id')->constrained('product')->onDelete('cascade');
            }
        });

        // Adiciona chave estrangeira na tabela 'product_sale'
        Schema::table('product_sale', function (Blueprint $table) {
            if (!Schema::hasColumn('product_sale', 'product_id')) {
                $table->foreignId('product_id')->constrained('product')->onDelete('cascade');
            }
            if (!Schema::hasColumn('product_sale', 'sale_id')) {
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
        // Remove as chaves estrangeiras adicionadas
        Schema::table('supplier', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
        });

        Schema::table('product', function (Blueprint $table) {
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

        Schema::table('product_price', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('product_sale', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['sale_id']);
        });
    }
};
