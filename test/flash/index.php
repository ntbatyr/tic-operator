<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

flashRedirect('/', [
    'status' => 'error',
    'message' => 'Прверка уведомления'
]);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
