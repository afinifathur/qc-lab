<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spectro_results', function (Blueprint $t) {
            // Tambah kolom hanya jika belum ada
            if (!Schema::hasColumn('spectro_results', 'co')) {
                $t->decimal('co', 8, 4)->nullable(); // Cobalt
            }
            if (!Schema::hasColumn('spectro_results', 'al')) {
                $t->decimal('al', 8, 4)->nullable(); // Aluminium
            }
            if (!Schema::hasColumn('spectro_results', 'v')) {
                $t->decimal('v', 8, 4)->nullable();  // Vanadium
            }
        });
    }

    public function down(): void
    {
        Schema::table('spectro_results', function (Blueprint $t) {
            // Drop hanya jika ada
            if (Schema::hasColumn('spectro_results', 'co')) {
                $t->dropColumn('co');
            }
            if (Schema::hasColumn('spectro_results', 'al')) {
                $t->dropColumn('al');
            }
            if (Schema::hasColumn('spectro_results', 'v')) {
                $t->dropColumn('v');
            }
        });
    }
};
