<?php
/**
 * @var $APPLICATION
 * @var $USER
 */

use Models\Database;

$APPLICATION->SetTitle('Интересное');

$userId = (int) $USER->GetID();
$db = new Database();


?>
<!--<link rel="stylesheet" href="/pages/assets/css/dashboard.css?<?/*=date('YmdHis')*/?>">

<div class="container-fluid">
    <?php /*include 'components/programs.widget.php'; */?>
    <?php /*include 'components/services.php'; */?>
</div>-->
