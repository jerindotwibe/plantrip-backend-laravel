<?php
// TEMPORARY MIGRATION RUNNER - DELETE AFTER USE!
define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre>";

echo "=== Running: config:clear ===\n";
$kernel->call('config:clear');
echo $kernel->output();

echo "\n=== Running: migrate ===\n";
$kernel->call('migrate', ['--force' => true]);
echo $kernel->output();

echo "\n✅ Done! DELETE this file now for security!";
echo "</pre>";
