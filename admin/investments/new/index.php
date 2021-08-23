<?php
/**
 * @var $USER
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

if (empty($_POST))
    flashRedirect('/admin/investments/', [
        'status' => 'error',
        'message' => 'Не заполнены поля'
    ]);

if (\Models\InvestProgram::create($_POST))
    flashRedirect('/admin/investments/', [
        'status' => 'success',
        'message' => 'Программа успешно добавлена'
    ]);

flashRedirect('/admin/investments/', [
    'status' => 'error',
    'message' => 'Не удалось добавить программу'
]);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");