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
        Schema::create('tickets', function (Blueprint $table) {
           $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            
            // Status ENUM
            $table->enum('status', ['open', 'in-progress', 'resolved', 'closed'])->default('open');
            
            // Priority ENUM
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            
            // User who created the ticket
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // User assigned to the ticket
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
