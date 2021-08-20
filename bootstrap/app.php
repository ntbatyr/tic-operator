<?php
const APP_ROOT = __DIR__ . '/../';

require APP_ROOT .'/vendor/autoload.php';
require APP_ROOT .'/helpers.php';

use Models\Database;

new Database();
