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
        Schema::create('articles', function (Blueprint $table) {
            $table->id('ArticleID');
            $table->string('ArticleName', 255)->unique();
            $table->text('Description')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->timestamps();
            $table->softDeletes('deleted_at');
            
            // Add index for search optimization
            $table->index('ArticleName');
            $table->index('IsActive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};