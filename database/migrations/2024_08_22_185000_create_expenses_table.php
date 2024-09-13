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
        Schema::create('expenses', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->date('date');
            $table->string('votehead_id');
            $table->decimal('amount',10,2)->default(0);
            $table->enum('payment_method', ['cash', 'mpesa', 'cheque','bank']);
            $table->string('payment_reference')->nullable();
            $table->string('reference')->nullable();
            $table->string('member_id')->nullable();
            $table->string('description')->nullable();
            $table->string('attachment_path')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->foreignId('user_id')->constrained();
            $table->timestamps();

            $table->foreign('votehead_id')->references('id')->on('vote_heads');
            $table->foreign('member_id')->references('id')->on('members');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
