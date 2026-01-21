<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('samples', function (Blueprint $t) {
            $t->id();
            $t->string('report_no')->unique()->nullable(); // diisi saat Submit
            $t->string('heat_no')->nullable();
            $t->string('batch_no')->nullable();
            $t->string('grade'); // 304/316
            $t->string('standard')->default('ASTM A351');
            $t->string('product_type')->nullable();
            $t->string('size_spec')->nullable();
            $t->string('po_no')->nullable();
            $t->string('customer')->nullable();
            $t->string('process')->nullable(); // forging/casting
            $t->date('test_date')->nullable();
            $t->string('machine_spektro')->nullable();
            $t->string('machine_tensile')->nullable();
            $t->string('machine_hardness')->nullable();
            $t->enum('overall_result', ['PASS','FAIL'])->nullable();
            $t->enum('status', ['DRAFT','SUBMITTED','APPROVED','REJECTED'])->default('DRAFT');
            $t->foreignId('created_by')->nullable()->constrained('users');
            $t->foreignId('approved_by')->nullable()->constrained('users');
            $t->timestamp('approved_at')->nullable();
            $t->unsignedInteger('version')->default(1);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('samples');
    }
};
