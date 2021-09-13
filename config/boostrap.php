<?php

use Dotenv\Dotenv;

require_once 'vendor/autoload.php';

if (is_file(dirname(__DIR__) . '/config/.env') &&
    is_readable(dirname(__DIR__) . '/config/.env')) {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__). '/config');
    $dotenv->load();
    $dotenv->required(['CRYPTO_SALT']);
}



