<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('position_comfort_category', function (Blueprint $table) {
            
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('comfort_category_id')->constrained()->cascadeOnDelete();
            $table->primary(['position_id', 'comfort_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('position_comfort_category');
    }
};
