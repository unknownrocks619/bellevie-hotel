<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pages', function (Blueprint $table) {
            $table->json('builder_data')->nullable()->after('content')
                ->comment('JSON array of builder sections');
            $table->boolean('use_builder')->default(false)->after('builder_data')
                ->comment('Whether to render this page via builder instead of content field');
        });
    }
    public function down(): void {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['builder_data', 'use_builder']);
        });
    }
};
