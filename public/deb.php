<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    if ($_ENV['SERVER_TYPE'] == 'local' || $_ENV['SERVER_TYPE'] == 'dev') {
        return new Kernel('prod', true);
    } else {
        return new Kernel('prod', false);
    }
};
