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
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('archive_id')->constrained('archives')->onDelete('cascade');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('letter_id')->nullable()->constrained('letters');
            $table->string('no_letter');
            $table->string('letter_code');
            $table->enum('type', ['letter_in', 'letter_out', 'faktur', 'memo', 'notulen'])->default('letter_out');
            $table->text('name');
            $table->enum('status', ['active', 'rusak', 'hilang', 'delete'])->default('active');
            $table->longText('content')->nullable();;
            $table->text('detail')->nullable();
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('place')->nullable();
            $table->text('event')->nullable();
            $table->string('chairman')->nullable();
            $table->string('chairman_position')->nullable();
            $table->string('notulis')->nullable();
            $table->longText('participant')->nullable();
            $table->longText('decision')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
