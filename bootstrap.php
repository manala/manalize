<?php

require_once __DIR__.'/vendor/autoload.php';

define('MANALIZE_DIR', __DIR__);
define('UPDATE_FIXTURES', filter_var(getenv('UPDATE_FIXTURES'), FILTER_VALIDATE_BOOLEAN));
