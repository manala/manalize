<?php

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

foreach (array(__DIR__.'/../../autoload.php', __DIR__.'/../vendor/autoload.php', __DIR__.'/vendor/autoload.php') as $autoload) {
    if (file_exists($autoload)) {
        require_once $autoload;

        break;
    }
}

if (PHP_MAJOR_VERSION < 7) {
    $io = new SymfonyStyle(new ArgvInput(), new ConsoleOutput());
    $io->error('PHP 7 is required in order to run manalize.');
    exit(1);
}

define('MANALIZE_DIR', __DIR__);
define('MANALIZE_TMP_ROOT_DIR', sys_get_temp_dir().'/Manala');
define('UPDATE_FIXTURES', filter_var(getenv('UPDATE_FIXTURES'), FILTER_VALIDATE_BOOLEAN));

/**
 * Creates a unique tmp dir.
 *
 * @param string $prefix
 *
 * @return string The path to the tmp dir created
 */
function manala_get_tmp_dir($prefix = '')
{
    if (!is_dir(MANALIZE_TMP_ROOT_DIR)) {
        @mkdir(MANALIZE_TMP_ROOT_DIR);
    }

    $tmp = @tempnam(MANALIZE_TMP_ROOT_DIR, $prefix);
    unlink($tmp);
    mkdir($tmp, 0777, true);

    return $tmp;
}
