<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sys_seo', function (Blueprint $table) {
            $table->id('id_seo');
            $table->string('relation_table');
            $table->string('relation_class');
            $table->unsignedBigInteger('relation_id');
            $table->string('title_seo')->nullable();
            $table->longText('description_seo')->nullable();
            $table->longText('tags_seo')->nullable();
            $table->string('feature_image_seo')->nullable();
            $table->timestamps();

            $table->index(['relation_table', 'relation_id'], 'seo_relation_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sys_seo');
    }
};
