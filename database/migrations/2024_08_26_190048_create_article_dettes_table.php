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
        Schema::create('article_dettes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_article');
            $table->unsignedBigInteger('id_dette');
            $table->integer('qteVente');
            $table->decimal('prixVente', 8, 2);
            $table->foreign('id_article')->references('id')->on('articles')->onDelete('cascade');
            $table->foreign('id_dette')->references('id')->on('dettes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_dettes');
    }
};
