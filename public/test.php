<?php

header('Content-Type: text/plain');

echo "PHP OK\n";
echo "PHP_VERSION=" . PHP_VERSION . "\n";
echo "PORT=" . ($_ENV['PORT'] ?? 'not-set') . "\n";
echo "SERVER_NAME=" . ($_ENV['SERVER_NAME'] ?? 'not-set') . "\n";