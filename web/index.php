<?php

/*
function explore(string $path, int $level = 0)
{
    echo str_repeat('  | - ', $level);
    if (is_file($path)) {
        echo '<strong style="color: #000;">' . basename($path) . '</strong>' . "\n";
    } else {
        echo basename($path) . " :\n";
        $files = glob("$path/*");
        foreach ($files as $file) {
            if (!in_array(basename($file), ['.', '..']) && !in_array($file, ['/code/web/../engine//vendors'])) {
                explore($file, $level + 1);
            }
        }
    }
}

echo '<pre style="width: 45%; display: inline-block; color: #777; vertical-align: top;">';
explore(__DIR__ . '/../app/');
echo '</pre>';
echo '<pre style="width: 50%; display: inline-block; color: #777">';
explore(__DIR__ . '/../engine/');
echo '</pre>';
exit;
*/

$thor_kernel = 'http';

require_once __DIR__ . '/../application.php';
