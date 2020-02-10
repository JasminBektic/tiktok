<?php

include __DIR__ . "/../vendor/autoload.php";

use TikTok\Database;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Constants
 */
define('TIKTOK_BASE_PATH', __DIR__ . '/../');

(new Dotenv)->load(TIKTOK_BASE_PATH . '.env');
(new Database)->initialize();