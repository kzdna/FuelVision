<?php

header('Content-Type: text/plain');

echo "STEP 1: PHP OK\n";
echo "PHP_VERSION=" . PHP_VERSION . "\n";
echo "PORT=" . ($_ENV['PORT'] ?? 'NULL') . "\n";
echo "SERVER_NAME=" . ($_ENV['SERVER_NAME'] ?? 'NULL') . "\n";

require __DIR__ . '/../vendor/autoload.php';

echo "STEP 2: Autoload OK\n";

$app = require_once __DIR__ . '/../bootstrap/app.php';

echo "STEP 3: Laravel app created\n";

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "STEP 4: HTTP kernel OK\n";

$request = Illuminate\Http\Request::capture();

echo "STEP 5: Request created\n";

echo "\nCONFIG VALUES:\n";

try {
    echo "session.driver=" . var_export(config('session.driver'), true) . "\n";
    echo "cache.default=" . var_export(config('cache.default'), true) . "\n";
    echo "database.default=" . var_export(config('database.default'), true) . "\n";
} catch (Throwable $e) {
    echo "\nCONFIG ERROR\n";
    echo "CLASS: " . get_class($e) . "\n";
    echo "MESSAGE: " . $e->getMessage() . "\n";
}

echo "\nENV CHECK:\n";
echo "SESSION_DRIVER=" . var_export($_ENV['SESSION_DRIVER'] ?? null, true) . "\n";
echo "CACHE_STORE=" . var_export($_ENV['CACHE_STORE'] ?? null, true) . "\n";
echo "APP_ENV=" . var_export($_ENV['APP_ENV'] ?? null, true) . "\n";

echo "\nDONE\n";