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
        Schema::create('rule_models', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('value_1_type');
            $table->string('value_1');
            $table->string('comparitor');
            $table->string('value_2_type');
            $table->string('value_2');
            $table->string('category', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rule_models');
    }
};
