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
        if (! Schema::hasTable('product_images')) {
            return;
        }

        Schema::table('product_images', function (Blueprint $table) {
            if (! Schema::hasColumn('product_images', 'product_id')) {
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('product_images', 'path')) {
                $table->string('path')->nullable();
            }

            if (! Schema::hasColumn('product_images', 'disk')) {
                $table->string('disk')->default('public');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('product_images')) {
            return;
        }

        Schema::table('product_images', function (Blueprint $table) {
            if (Schema::hasColumn('product_images', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
            }

            $columns = [];

            foreach (['path', 'disk'] as $column) {
                if (Schema::hasColumn('product_images', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
