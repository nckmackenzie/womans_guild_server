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
        Schema::create('closing_balances', function (Blueprint $table) {
            $table->id();
            $table->string('year_id');
            $table->string('member_id');
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->foreign('year_id')->references('id')->on('years')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('closing_balances');
    }
};
