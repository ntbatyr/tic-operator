<?php
/**
 * @var $USER
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$iPrograms = \Models\InvestProgram::all();
?>

<div class="container-fluid">
    <? if ($iPrograms->isEmpty()) { ?>
        <div class="alert alert-danger">Нет програм</div>
    <? } else {
        foreach ($iPrograms as $p) { ?>
            <a class="btn btn-primary px-5 my-3" href="/investments/entrance/?program=<?=$p->id?>">Вступить в программу <?=$p->name?> минимальный взнос <?=number_format($p->min_deposit, 2, '.', ' ')?> руб.</a>
        <? }
    }?>
</div>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");