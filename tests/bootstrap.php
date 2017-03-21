<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

use Manala\Manalize\Template\Syncer;

require_once __DIR__.'/../bootstrap.php';

define('MANALIZE_HOME', __DIR__.'/fixtures');
define('UPDATE_FIXTURES', filter_var(getenv('UPDATE_FIXTURES'), FILTER_VALIDATE_BOOLEAN));

(new Syncer())->sync('e91a633');
