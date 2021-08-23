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
    <form action="/investments/entrance/" method="post">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-6">
                <?php
                $total = 0;
                foreach ($iPrograms as $p) {
                    $total += $p['MinDeposit'];
                    ?>
                    <div class="p-3 my-2 shadow rounded">
                        <div class="row">
                            <div class="col-2">
                                <input type="checkbox" name="<?=$p['Slug']?>" id="<?=$p['Slug']?>" class="form-control" checked>
                            </div>
                            <div class="col-10">
                                <label><?=$p['Name']?> <?=$p['Annual']?>% годовых, минимальный взнос <?=number_format($p['MinDeposit'], 2, '.', ' ');?> руб.</label>
                            </div>
                            <div class="col-12">
                                <input type="number" class="form-control" name="<?=$p['Slug']?>_custom_deposit" min="<?=$p['MinDeposit']?>" value="<?=$p['MinDeposit']?>">
                            </div>
                        </div>
                    </div>
                <? }?>

                <div class="h4">Итого: <span id="total-amount"><?=number_format($total, 2, '.', ' ');?></span> руб. к оплате</div>

                <div class="d-flex align-items-center justify-content-center mt-5">
                    <button type="submit" class="btn btn-success">Вступить</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");