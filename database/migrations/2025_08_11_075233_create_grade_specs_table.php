<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grade_specs', function (Blueprint $t) {
            $t->id();
            $t->string('grade'); // 304/316
            $t->string('standard')->default('ASTM A351');
            $t->string('property_key'); // C, Cr, Ni, YS, UTS, Elong, HB, dll
            $t->decimal('min_val', 10, 3)->nullable();
            $t->decimal('max_val', 10, 3)->nullable();
            $t->string('unit'); // %wt, MPa, %, HB
            $t->timestamps();
            $t->unique(['grade','standard','property_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_specs');
    }
};
