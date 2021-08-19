<?php


if (!function_exists('app_config')) {
    function app_config($name = ''): array
    {
        if(empty($name))
            return [];

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] ."/app_configs/{$name}.php"))
            return [];

        include $_SERVER['DOCUMENT_ROOT'] ."/app_configs/{$name}.php";
    }
}