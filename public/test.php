<?php

header('Content-Type: text/plain');

echo "STEP 1: PHP OK\n";

try {
    require __DIR__ . '/../vendor/autoload.php';

    echo "STEP 2: Autoload OK\n";

    $app = require_once __DIR__ . '/../bootstrap/app.php';

    echo "STEP 3: Laravel app created\n";

    $app->make(\Illuminate\Contracts\Console\Kernel::class);

    echo "STEP 4: Laravel kernel OK\n";

} catch (\Throwable $e) {
    echo "LARAVEL ERROR\n";
    echo "CLASS: " . get_class($e) . "\n";
    echo "MESSAGE: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . "\n";
    echo "LINE: " . $e->getLine() . "\n";
    echo "\nTRACE:\n";
    echo $e->getTraceAsString();
}