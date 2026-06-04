<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            // Drop image columns — images are now stored in the images table
            // and linked via image_relations (polymorphic pivot)
            if (Schema::hasColumn('galleries', 'image_url')) {
                $table->dropColumn('image_url');
            }
            if (Schema::hasColumn('galleries', 'cloudinary_public_id')) {
                $table->dropColumn('cloudinary_public_id');
            }
            // Ensure description exists
            if (!Schema::hasColumn('galleries', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('description');
            $table->string('cloudinary_public_id')->nullable()->after('image_url');
        });
    }
};
