<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $t) {
            $t->softDeletes();
        });
        Schema::table('spectro_results', function (Blueprint $t) {
            $t->softDeletes();
        });
        Schema::table('tensile_tests', function (Blueprint $t) {
            $t->softDeletes();
        });
        Schema::table('hardness_tests', function (Blueprint $t) {
            $t->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('samples', fn (Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('spectro_results', fn (Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('tensile_tests', fn (Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('hardness_tests', fn (Blueprint $t) => $t->dropSoftDeletes());
    }
};
