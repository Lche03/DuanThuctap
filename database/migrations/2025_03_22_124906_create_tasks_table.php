<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['Chưa bắt đầu', 'Đang thực hiện', 'Đã hoàn thành'])->default('Chưa bắt đầu');
            $table->date('due_date');
            $table->foreignId('creator_id')->constrained('users');
            $table->foreignId('assignee_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
