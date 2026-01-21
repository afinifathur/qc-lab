<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('samples', 'po_customer')) {
            Schema::table('samples', function (Blueprint $table) {
                $table->string('po_customer', 100)->nullable()->after('batch_no');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('samples', 'po_customer')) {
            Schema::table('samples', function (Blueprint $table) {
                $table->dropColumn('po_customer');
            });
        }
    }
};
