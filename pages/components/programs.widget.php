<?php
/**
 * @var array $programs
 * @var int $userId
 */

$programBackgrounds = [
    'apple' => 'zanah',
    'samsung' => 'solitude',
    'cars' => 'zanah',
    'realty' => 'zanah',
];

if (empty($programs)) {
    require_once $_SERVER['DOCUMENT_ROOT'] .'/programs/helper.php';
    $programs = getUserProgramsList($userId);
}?>

<div class="row">
<? foreach ($programs as $programItem) {
    $queueNumber = getProgramQueueNumber($programItem['Program'], $programItem['creID']);
    $totalQueue = getProgramQueueNumber($programItem['Program']);
?>
    <div class="col-12 col-md-6 col-lg-4 my-2">
        <a class="program-intro-block floating-card bg-<?=$programBackgrounds[$programItem['Program']];?>" href="/programs/<?=$programItem['Program'];?>/?id=<?=$programItem['creID'];?>">
            <div class="d-flex align-items-center justify-content-between">
                <div class="icon-raw">
                    <img src="/pages/assets/img/<?=$programItem['Program']?>-logo.svg" class="h-100">
                </div>
                <span class="btn-context"></span>
            </div>

            <div class="d-flex align-items-end justify-content-between mt-5">
                <div class="fs28px"><?=ucfirst(getProgramName($programItem['Program']))?></div>
                <div class="progress-block">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="fs16px w100px">Ваша очередь</div>
                        <div class="fs50px fw-bold"><?=$queueNumber?></div>
                    </div>
                    <div class="progress-bar" data-all="<?=$totalQueue?>" data-queue="<?=$queueNumber?>">
                        <div class="layout-all">
                            <div class="layout-queue"></div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
<?php } ?>

    <div class="col-12 col-md-6 col-lg-4 my-2">
        <a class="program-entrance-block">
            <img src="/pages/assets/img/plus-grey.svg" class="icon-add-new">
        </a>
    </div>
</div>
