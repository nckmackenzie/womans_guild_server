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
        Schema::create('budget_details', function (Blueprint $table) {
            $table->id();
            $table->string('header_id');
            $table->string('votehead_id');
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->string('description')->nullable();
            
            $table->foreign('header_id')->references('id')->on('budget_headers')->onDelete('cascade');
            $table->foreign('votehead_id')->references('id')->on('vote_heads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_details');
    }
};
