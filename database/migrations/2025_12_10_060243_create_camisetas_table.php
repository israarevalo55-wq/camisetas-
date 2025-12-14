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
        Schema::create('camisetas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion')->nullable(); #permite descripciones largas o cortas

            //columna de los precios
            $table->decimal('precio_normal', 8, 2)->nullable();
            $table->decimal('precio_oferta', 8, 2)->nullable();

            //fires base storage 
            $table->string('imagen_url',500)->nullable();

            //estado de la camiseta
            $table->boolean('disponible')->default(true);
            $table->integer('stock')->default(0);

            //relacion con la plataforma  1 a muchos
            $table->foreignId('plataforma_id')
            ->nullable()->constrained('plataformas')
            ->nullOnDelete('set null'); //si se elimina la plataforma las camisetas 
            // se quedan sin plaforma (no se borra)




            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camisetas');
    }
};
