<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

$routes = $app['router']->getRoutes();
$count = 0;

echo "Total routes: " . count($routes) . "\n\n";

foreach ($routes as $route) {
    if (strpos($route->uri(), 'api') !== false || strpos(implode(',', $route->methods()), 'DELETE') !== false) {
        echo $route->methods()[0] . " " . $route->uri() . "\n";
        $count++;
    }
}

echo "\nFound $count API routes\n";
?>
