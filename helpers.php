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

if (!function_exists('flashRedirect')){
    function flashRedirect(string $url, array $content)
    {
        global $_SESSION;

        $_SESSION['flash'] = $content;
        header("Location: {$url}");
    }
}

if (!function_exists('getInvestmentPrograms')) {
    function getInvestmentPrograms()
    {
        global $db;

        $q = "select * from InvestmentPrograms";
        return $db->query($q)->fetchAll();
    }
}