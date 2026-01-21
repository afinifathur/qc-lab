<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hardness_tests', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sample_id')->constrained('samples')->cascadeOnDelete();
            $t->string('method')->default('HB'); // HB/HRC/HV
            $t->string('scale')->nullable();
            $t->decimal('load_kgf', 8, 2)->nullable();
            $t->decimal('location1', 6, 2)->nullable();
            $t->decimal('location2', 6, 2)->nullable();
            $t->decimal('location3', 6, 2)->nullable();
            $t->decimal('avg_value', 6, 2)->nullable();
            $t->boolean('pass_bool')->nullable();
            $t->text('remarks')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hardness_tests');
    }
};
