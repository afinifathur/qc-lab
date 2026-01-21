<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('spectro_results', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sample_id')->constrained('samples')->cascadeOnDelete();

            // Unsur utama (% berat). Simpan 3 desimal, nanti ditampilkan 2 desimal di PDF.
            $t->decimal('c', 6, 3)->nullable();
            $t->decimal('si', 6, 3)->nullable();
            $t->decimal('mn', 6, 3)->nullable();
            $t->decimal('p', 6, 3)->nullable();
            $t->decimal('s', 6, 3)->nullable();
            $t->decimal('cr', 6, 3)->nullable();
            $t->decimal('ni', 6, 3)->nullable();
            $t->decimal('mo', 6, 3)->nullable();
            $t->decimal('cu', 6, 3)->nullable();
            $t->decimal('n', 6, 3)->nullable();

            // Opsional tambahan
            $t->decimal('al', 6, 3)->nullable();
            $t->decimal('v', 6, 3)->nullable();
            $t->decimal('co', 6, 3)->nullable();
            $t->decimal('ti', 6, 3)->nullable();
            $t->decimal('nb', 6, 3)->nullable();
            $t->decimal('w', 6, 3)->nullable();
            $t->decimal('fe', 6, 3)->nullable();

            $t->boolean('pass_bool')->nullable();
            $t->text('remarks')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spectro_results');
    }
};
