<?php
/**
 * @var $APPLICATION
 * @var $USER
 */

$APPLICATION->SetTitle('Интересное');

$userId = (int) $USER->GetID();
$db = \Bitrix\Main\Application::getConnection('server');

?>
<link rel="stylesheet" href="/pages/assets/css/dashboard.css?<?=date('YmdHis')?>">

<div class="container-fluid">
    <?php include 'components/programs.widget.php'; ?>
    <?php include 'components/services.php'; ?>
</div>
