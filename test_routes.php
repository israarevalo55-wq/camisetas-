<?php
require 'vendor/autoload.php';

try {
    require 'bootstrap/app.php';
    echo "App loaded\n";
    
    echo "Testing imports...\n";
    echo "GeneroController: " . class_exists('App\Http\Controllers\Api\GeneroController') . "\n";
    echo "PlataformaController: " . class_exists('App\Http\Controllers\Api\PlataformaController') . "\n";
    echo "CamisetaController: " . class_exists('App\Http\Controllers\Api\CamisetaController') . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine() . "\n";
}
?>
