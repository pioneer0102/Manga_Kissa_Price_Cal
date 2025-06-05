<?php

declare(strict_types=1);
// PHP Syntax Checker Script
// Checks syntax of all PHP files in src/ and tests/ directories

$errors = 0;
$directories = ['src', 'tests'];

echo 'Checking PHP syntax...' . PHP_EOL;

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        continue;
    }

    $files = glob($dir . '/*.php');

    foreach ($files as $file) {
        $output = [];
        $return = 0;
        exec('php -l ' . escapeshellarg($file), $output, $return);

        if (0 !== $return) {
            echo "❌ Syntax error in {$file}:" . PHP_EOL;

            foreach ($output as $line) {
                echo '   ' . $line . PHP_EOL;
            }
            ++$errors;
        } else {
            echo "✅ {$file}" . PHP_EOL;
        }
    }
}

echo PHP_EOL;

if ($errors > 0) {
    echo "❌ Found {$errors} syntax error(s)." . PHP_EOL;
    exit(1);
}
echo '✅ No syntax errors found.' . PHP_EOL;
exit(0);
