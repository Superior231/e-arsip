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
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('division_id')->constrained('divisions');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('archive_id');
            $table->string('archive_code');
            $table->string('name');
            $table->enum('status', ['pending', 'approve', 'delete'])->default('pending');
            $table->string('image')->nullable();
            $table->text('detail')->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
