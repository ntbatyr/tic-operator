<?php
/**
 * @var $USER
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$db = \Bitrix\Main\Application::getConnection('server');

$iPrograms = getInvestmentPrograms();

dd($iPrograms);
?>

<div class="container-fluid">

</div>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");