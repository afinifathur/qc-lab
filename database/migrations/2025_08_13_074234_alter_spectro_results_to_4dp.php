<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: ubah kolom komposisi → DECIMAL(6,4)
        DB::statement("ALTER TABLE spectro_results
            MODIFY c  DECIMAL(6,4) NULL,
            MODIFY si DECIMAL(6,4) NULL,
            MODIFY mn DECIMAL(6,4) NULL,
            MODIFY p  DECIMAL(6,4) NULL,
            MODIFY s  DECIMAL(6,4) NULL,
            MODIFY cr DECIMAL(6,4) NULL,
            MODIFY ni DECIMAL(6,4) NULL,
            MODIFY mo DECIMAL(6,4) NULL,
            MODIFY cu DECIMAL(6,4) NULL,
            MODIFY n  DECIMAL(6,4) NULL
        ");
    }

    public function down(): void
    {
        // Kembalikan ke 2 desimal kalau perlu (silakan sesuaikan jika awalnya 3)
        DB::statement("ALTER TABLE spectro_results
            MODIFY c  DECIMAL(5,2) NULL,
            MODIFY si DECIMAL(5,2) NULL,
            MODIFY mn DECIMAL(5,2) NULL,
            MODIFY p  DECIMAL(5,2) NULL,
            MODIFY s  DECIMAL(5,2) NULL,
            MODIFY cr DECIMAL(5,2) NULL,
            MODIFY ni DECIMAL(5,2) NULL,
            MODIFY mo DECIMAL(5,2) NULL,
            MODIFY cu DECIMAL(5,2) NULL,
            MODIFY n  DECIMAL(5,2) NULL
        ");
    }
};
