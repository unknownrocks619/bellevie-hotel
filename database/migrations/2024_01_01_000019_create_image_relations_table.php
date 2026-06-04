<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_relations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('image_id')->index();
            $table->foreign('image_id')->references('id')->on('images')->onDelete('cascade');

            // Polymorphic-style relation — stores the owning model's table + id
            $table->string('relation')->comment('Owner table name, e.g. rooms, blog_posts');
            $table->unsignedBigInteger('relation_id');
            $table->index(['relation', 'relation_id'], 'img_rel_poly_index');

            // Optional metadata about this specific usage
            $table->string('type')->nullable()->comment('featured | gallery | seo | logo | …');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_relations');
    }
};
