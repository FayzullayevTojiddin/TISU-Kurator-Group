<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weeks', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedTinyInteger('week_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['year', 'month', 'week_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weeks');
    }
};
