<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(config('filament-tracer.database.table_name'), function (Blueprint $table) {
            $table->bigIncrements(config('filament-tracer.database.primary_key'))->primary();
            $table->string('type', 255)->default('exception');
            $table->string('code', 12);
            $table->text('message');
            $table->string('file', 100);
            $table->integer('line');
            $table->text('trace');
            $table->string('method', 100);
            $table->string('path', 100);
            $table->text('query')->nullable();
            $table->text('body')->nullable();
            $table->text('cookies')->nullable();
            $table->text('headers')->nullable();
            $table->string('ip', 64);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracers');
    }
};
