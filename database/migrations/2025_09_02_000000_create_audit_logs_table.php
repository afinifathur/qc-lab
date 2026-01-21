<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('stamp_id');                 // UUID v4
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();  // Operator|Approver|Auditor
            $table->string('action');                 // preview_pdf, download_pdf, print_pdf, store_sample, ...
            $table->string('entity_type')->nullable();// App\Models\Sample
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('route')->nullable();
            $table->string('method', 10)->nullable();
            $table->string('ip', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['entity_type','entity_id']);
            $table->index(['action']);
            $table->index(['user_id']);
            $table->index(['stamp_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('audit_logs');
    }
};
