<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->integer('rooms_wanted')->nullable()->after('budget');
            $table->integer('area_min')->nullable()->after('rooms_wanted');
            $table->integer('area_max')->nullable()->after('area_min');
            $table->string('preferred_district')->nullable()->after('area_max');
            $table->enum('payment_method', ['cash', 'mortgage', 'installment', 'mixed'])->nullable()->after('preferred_district');
            $table->enum('urgency', ['immediate', '1_3_months', '3_6_months', 'later'])->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['rooms_wanted', 'area_min', 'area_max', 'preferred_district', 'payment_method', 'urgency']);
        });
    }
};
