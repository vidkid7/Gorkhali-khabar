<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_pages')) {
            Schema::create('content_pages', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('slug')->unique();
                $table->string('title');
                $table->string('title_en')->nullable();
                $table->longText('body');
                $table->longText('body_en')->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestampsTz();
            });
        }

        if (! Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('location')->index();
                $table->string('label');
                $table->string('label_en')->nullable();
                $table->string('href');
                $table->string('parent_id')->nullable()->index();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestampsTz();
            });
        }

        if (! Schema::hasTable('homepage_sections')) {
            Schema::create('homepage_sections', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('section_key')->unique();
                $table->string('title');
                $table->string('title_en')->nullable();
                $table->string('category_slug')->nullable();
                $table->string('layout')->default('grid');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestampsTz();
            });
        }

        if (! Schema::hasTable('live_blogs')) {
            Schema::create('live_blogs', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('slug')->unique();
                $table->string('title');
                $table->string('title_en')->nullable();
                $table->text('summary')->nullable();
                $table->text('summary_en')->nullable();
                $table->string('status')->default('DRAFT');
                $table->timestampTz('started_at')->nullable();
                $table->timestampTz('ended_at')->nullable();
                $table->timestampsTz();
            });
        }

        if (! Schema::hasTable('live_blog_posts')) {
            Schema::create('live_blog_posts', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('live_blog_id')->index();
                $table->string('title')->nullable();
                $table->longText('body');
                $table->longText('body_en')->nullable();
                $table->string('author_id')->nullable()->index();
                $table->timestampTz('published_at')->nullable();
                $table->timestampsTz();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('live_blog_posts');
        Schema::dropIfExists('live_blogs');
        Schema::dropIfExists('homepage_sections');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('content_pages');
    }
};
