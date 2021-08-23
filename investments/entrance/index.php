<?php
/**
 * @var CUser $USER
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$db = \Bitrix\Main\Application::getConnection('server');

$iPrograms = getInvestmentPrograms();

if (empty($_POST))
    flashRedirect('/investments/', [
        'status' => 'error',
        'message' => 'Вам нужно выбрать программу для вступления'
    ]);

$total = 0;
$cantAdd = false;

foreach ($iPrograms as $p) {
    if (!isset($_POST[$p['Slug']]))
        continue;

    $deposit = $_POST["{$p['Slug']}_custom_amount"];
    $total += $deposit;

    $create = \Models\UserInvestProgram::create([
        'users_id' => $USER->GetID(),
        'invest_program_id' => $p['ID'],
        'amount' => $deposit
    ]);

    if (!$create) {
        $cantAdd = true;
        break;
    }
}

if ($cantAdd)
    flashRedirect('/investment/', [
        'status' => 'error',
        'message' => 'Не удалось вступить в программу. Пожалуйста обратитесь в службу поддержки для устранения причин.'
    ]);


$formattedTotal = number_format($total, 2, '.', ' ');

flashRedirect('/', [
    'status' => 'success',
    'message' => "Отлично! Вы выбрали свои инвестиционные программы на сумму {$formattedTotal} руб."
]);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
