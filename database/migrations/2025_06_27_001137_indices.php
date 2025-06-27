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
        Schema::create('indices', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('livro_id');
            $table->foreign('livro_id')->references('id')->on('livros')->onDelete('cascade');

            $table->unsignedBigInteger('indice_pai_id')->nullable();
            $table->foreign('indice_pai_id')->references('id')->on('indices')->onDelete('cascade');

            $table->string('titulo', 200);
            $table->integer('pagina');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indices');

    }
};
