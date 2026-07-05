<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('website_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->enum('page_type', ['home', 'about', 'admissions', 'contact', 'news', 'events', 'gallery', 'custom'])->default('custom');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_homepage')->default(false);
            $table->integer('display_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'slug']);
            $table->index(['school_id', 'is_published']);
        });

        Schema::create('page_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_page_id')->constrained('website_pages')->cascadeOnDelete();
            $table->integer('revision_number');
            $table->longText('html_content')->nullable();
            $table->longText('css_content')->nullable();
            $table->longText('components_json')->nullable(); // GrapesJS model JSON
            $table->boolean('is_current_draft')->default(true);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('website_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category'); // Layout, Content, Media, Dynamic, Contact
            $table->longText('html_template');
            $table->string('preview_image')->nullable();
            $table->boolean('is_dynamic')->default(false);
            $table->string('dynamic_source')->nullable(); // news, events, staff, etc.
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('site_name');
            $table->string('site_tagline')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('primary_color', 10)->default('#003366');
            $table->string('secondary_color', 10)->default('#FFD700');
            $table->string('accent_color', 10)->default('#FF6B35');
            $table->string('text_color', 10)->default('#333333');
            $table->string('bg_color', 10)->default('#FFFFFF');
            $table->string('heading_font')->default('Outfit');
            $table->string('body_font')->default('Inter');
            $table->string('social_facebook')->nullable();
            $table->string('social_twitter')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_youtube')->nullable();
            $table->string('google_analytics_id')->nullable();
            $table->text('custom_header_scripts')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('contact_map_embed')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('website_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('location'); // header, footer
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('website_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('website_menus')->cascadeOnDelete();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('label');
            $table->string('url')->nullable();
            $table->foreignId('page_id')->nullable()->constrained('website_pages')->nullOnDelete();
            $table->boolean('open_new_tab')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('website_menu_items')->nullOnDelete();
        });

        Schema::create('website_gallery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->unsignedBigInteger('album_id')->nullable(); // album categorization
            $table->integer('display_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('website_gallery_items');
        Schema::dropIfExists('website_menu_items');
        Schema::dropIfExists('website_menus');
        Schema::dropIfExists('website_settings');
        Schema::dropIfExists('website_blocks');
        Schema::dropIfExists('page_revisions');
        Schema::dropIfExists('website_pages');
    }
};
