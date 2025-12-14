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
        Schema::create('camiseta_genero', function (Blueprint $table) {
            $table->id();

            //fk camiseta
            $table->foreignId('camiseta_id')
            ->constrained('camisetas')
            ->cascadeOnDelete(); //si se elimina la camiseta se elimina la relacion

            //fk genero
            $table->foreignId('genero_id')
            ->constrained('generos')
            ->cascadeOnDelete(); //si se elimina el genero se elimina la relacion   
            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camiseta_genero');
    }
};
