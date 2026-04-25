<?php

require __DIR__ . '/../vendor/autoload.php';

// Pasta temporária para testes
define('TEST_DIR', __DIR__ . '/tmp');

if (!is_dir(TEST_DIR)) {
    mkdir(TEST_DIR, 0777, true);
}
