<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable');
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('origin', 10);
            $table->string('type', 30);
            $table->string('title');
            $table->string('disk', 20)->default('local');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->char('checksum', 64)->nullable();
            $table->boolean('is_visible_to_client')->default(false);
            $table->timestamp('visible_at')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['client_id', 'is_visible_to_client']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};