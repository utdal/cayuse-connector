<?php

require '../vendor/autoload.php';

define('APP_VERSION', '0.1.0');

(new Symfony\Component\Dotenv\Dotenv())->usePutenv()->load(dirname(__DIR__).'/.env');

(new App\Http\Router())->handle();