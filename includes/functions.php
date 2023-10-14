<?php

use EnvForWordpress\EnvForWordpress;

if (!function_exists('env')) {
    function env(string $key, $default = null) {
        return EnvForWordpress::get($key, $default);
    }
}
