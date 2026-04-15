<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'name')) {
                $table->string('name');
            }

            if (! Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable();
            }

            if (! Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 10, 2)->default(0);
            }

            if (! Schema::hasColumn('products', 'stock')) {
                $table->unsignedInteger('stock')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $columns = [];

            foreach (['name', 'description', 'price', 'stock'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
