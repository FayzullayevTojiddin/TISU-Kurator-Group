<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('curator')->after('name');
            $table->foreignId('faculty_id')->nullable()->after('role')
                ->constrained('faculties')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('faculty_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->dropColumn(['role', 'faculty_id', 'is_active']);
        });
    }
};
