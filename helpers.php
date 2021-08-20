<?php


if (!function_exists('app_config')) {
    function app_config($name = ''): array
    {
        if(empty($name))
            return [];

        if (!file_exists(APP_ROOT."/app_configs/{$name}.php"))
            return [];

        return require APP_ROOT ."/app_configs/{$name}.php";
    }
}