<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->nullable()->constrained('investors')->nullOnDelete();
            $table->string('title');
            $table->string('address');
            $table->decimal('price', 15, 2);
            $table->enum('status', ['free', 'sold', 'rent'])->default('free');
            $table->integer('rooms')->default(1);
            $table->integer('floor')->nullable();
            $table->integer('total_floors')->nullable();
            $table->integer('area')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};