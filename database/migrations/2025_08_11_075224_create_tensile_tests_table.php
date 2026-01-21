<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tensile_tests', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sample_id')->constrained('samples')->cascadeOnDelete();

            $t->decimal('ys_mpa', 8, 2)->nullable();     // Yield Strength
            $t->decimal('uts_mpa', 8, 2)->nullable();    // Ultimate Tensile Strength
            $t->decimal('elong_pct', 5, 2)->nullable();  // Elongation
            $t->decimal('ra_pct', 5, 2)->nullable();     // Reduction of Area (opsional)

            $t->string('method_std')->default('ASTM E8');
            $t->string('specimen_dims')->nullable();

            $t->boolean('pass_bool')->nullable();
            $t->text('remarks')->nullable();

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tensile_tests');
    }
};
