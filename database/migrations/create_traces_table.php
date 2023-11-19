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
            $table->string('source', 255);
            $table->string('error_type', 255)->default('Error');
            $table->string('path', 100);
            $table->string('ip', 64);
            $table->string('code', 12);
            $table->text('message');
            $table->integer('line');
            $table->string('method', 100);
            $table->string('file', 100);
            $table->text('traces')->nullable();
            $table->text('queries')->nullable();
            $table->text('body')->nullable();
            $table->text('headers')->nullable();
            $table->text('cookies')->nullable();
            $table->timestamp('created_at', 0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('filament-tracer.database.table_name'));
    }
};
