<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Genero;

try {
    $generos = Genero::all();
    echo "Total generos en BD: " . count($generos) . "\n";
    if (count($generos) > 0) {
        echo "Contenido:\n";
        foreach ($generos as $genero) {
            echo "ID: " . $genero->id . " - Nombre: " . $genero->nombre . "\n";
        }
    } else {
        echo "La tabla generos está vacía\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
