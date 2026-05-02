<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->enum('source', ['call', 'website', 'referral', 'social', 'walk_in', 'other'])->default('other');
            $table->enum('status', ['new', 'contacted', 'qualified', 'negotiating', 'won', 'lost'])->default('new');
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->decimal('budget', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->date('next_follow_up')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('operator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
