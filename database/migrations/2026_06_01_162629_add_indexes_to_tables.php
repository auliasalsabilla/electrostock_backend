<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToTables extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->index('name');
            $table->index('category_id');
            $table->index('supplier_id');
            $table->index('is_active');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('transaction_date');
            $table->index('item_id');
            $table->index('user_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['transaction_date']);
            $table->dropIndex(['item_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['type']);
        });
    }
}