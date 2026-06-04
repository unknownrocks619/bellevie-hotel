<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();

            // Cloudinary identifiers
            $table->string('public_id')->nullable()->unique()->comment('Cloudinary public_id — used for transforms and deletion');
            $table->string('version')->nullable()->comment('Cloudinary version string');

            // URLs
            $table->text('url')->comment('Secure CDN URL (https)');
            $table->text('url_thumb')->nullable()->comment('Auto-generated 200×200 thumbnail URL');

            // File metadata
            $table->string('original_filename');
            $table->string('format', 20)->nullable()->comment('jpg, png, webp, gif, …');
            $table->string('resource_type', 20)->default('image')->comment('image | video | raw');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedBigInteger('bytes')->nullable()->comment('File size in bytes');

            // Organisation
            $table->string('folder')->nullable()->comment('Cloudinary folder path');
            $table->string('source', 20)->default('cloudinary')->comment('cloudinary | local');

            // Raw Cloudinary response for future use
            $table->json('metadata')->nullable()->comment('Full Cloudinary upload response JSON');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
