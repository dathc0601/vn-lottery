<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('path_pattern', 500);
            $table->enum('match_type', ['exact', 'wildcard']);
            $table->string('label', 255)->nullable();
            $table->string('page_title', 500)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('og_title', 500)->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image', 500)->nullable();
            $table->string('canonical_url', 500)->nullable();
            $table->string('robots', 100)->nullable();
            $table->json('schema_jsonld')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'match_type', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_overrides');
    }
};
