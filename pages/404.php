<?php
$previousRoute = $_SERVER['HTTP_REFERER'] ?? '/';
?>

<div class="container-fluid">
    <h1 class="text-center mt-5">Такой страницы нет</h1>
    <div class="d-flex align-items-center justify-content-center mt-3">
        <a class="btn btn-primary" href="/">На главную</a>
        <? if ($previousRoute !== '/') { ?>
            <a class="btn btn-primary ml-3" href="<?=$previousRoute;?>">Назад</a>
        <? } ?>
    </div>
</div>