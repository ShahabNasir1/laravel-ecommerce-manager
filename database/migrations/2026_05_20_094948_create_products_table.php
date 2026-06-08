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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name', 100);
            $table->text('description')->nullable();
            // In Laravel, decimal needs a precision and scale. Defaulting here to 8,2 (e.g., 999999.99)
            $table->decimal('price', 8, 2);

            // Foreign Key columns mapping
            $table->foreignId('category_id')->constrained('categories', 'category_id')->onDelete('cascade');
            $table->foreignId('brand_id')->constrained('brands', 'brand_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'id')->onDelete('cascade');

            $table->enum('product_status', ['active', 'inactive']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
