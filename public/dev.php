<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    if ($_ENV['SERVER_TYPE'] == 'local') {
        return new Kernel('dev', true);
    } else {
        return new Kernel('prod', false);
    }
};
