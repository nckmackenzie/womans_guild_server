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
        Schema::create('members', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->integer('member_no')->unique();
            $table->string('name');
            $table->string('contact')->unique();
            $table->date('birth_date')->nullable();
            $table->string('id_number')->nullable()->unique();
            $table->date('joining_date')->nullable();
            $table->enum('status',['active','inactive','departed','deceased'])->default('active');
            $table->decimal('contribution_amount', 10, 2)->nullable()->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
