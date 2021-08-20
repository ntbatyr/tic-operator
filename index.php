<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); ?>

<?php
$dbConf = app_config('database');

dd($dbConf);
?>

<?php
/**
 * @var CUser $USER
 */

if ($USER->IsAuthorized())
    include 'pages/dashboard.php';
else
    include 'pages/landing.php'
?>


<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>
