<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); ?>

<?php
/**
 * @var CUser $USER
 */

if ($USER->IsAuthorized())
    include 'pages/dashboard.php';
else
    include 'pages/landing.php'
?>


<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>
