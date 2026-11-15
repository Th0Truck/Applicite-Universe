<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gallery_gallery_image', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gallery_image_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['gallery_id', 'gallery_image_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_gallery_image');
    }
};
