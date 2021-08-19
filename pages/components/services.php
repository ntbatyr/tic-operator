<?php

$services = [
    [
        'name' => 'Apple',
        'class' => 'apple-service-block',
        'slug' => 'apple',
        'icon' => 'apple-logo.svg',
        'path' => '/programs/apple/'
    ],
    [
        'name' => 'Samsung',
        'class' => 'samsung-service-block',
        'slug' => 'samsung',
        'icon' => 'samsung-logo.svg',
        'path' => '/programs/samsung/'
    ],
    [
        'name' => 'АвтоТИК',
        'fullName' => 'Автопрограмма',
        'class' => 'cars-service-block',
        'slug' => 'cars',
        'icon' => 'cars-logo.svg',
        'path' => '/programs/cars/'
    ],
    [
        'name' => 'ДомТИК',
        'fullName' => 'Жилищная программа',
        'class' => 'realty-service-block',
        'slug' => 'cars',
        'icon' => 'realty-logo.svg',
        'path' => '/programs/realty/'
    ],
    [
        'name' => 'Маркетплейс',
        'fullName' => 'Магазин мерча',
        'class' => 'marketplace-service-block',
        'slug' => 'marketplace',
        'icon' => 'bag.svg',
        'path' => '/marketplace/',
    ],
    [
        'name' => 'OCEANPAY',
        'class' => 'oceanpay-service-block',
        'slug' => 'cars',
        'icon' => 'cron.svg',
        'path' => '/oceanpay/'
    ],
    [
        'name' => 'CRON',
        'class' => 'cron-service-block',
        'slug' => 'cars',
        'icon' => 'cron.svg',
        'path' => '/cron/'
    ],
    [
        'name' => 'Накопительная',
        'class' => 'savings-service-block',
        'slug' => 'savings',
        'icon' => 'wallet.svg',
        'path' => '/savings/'
    ],
];?>

<div class="fs25px fw-bold mt-5 mb-4">Все программы</div>

<div class="row">
    <?php foreach ($services as $service) { ?>
        <div class="col-12 col-md-6 col-lg-4 my-3">
            <a class="service-block <?=$service['class'];?> rounded30px floating-card" href="<?=$service['path'];?>">
                <img src="/pages/assets/img/<?=$service['icon'];?>">
                <div>
                    <div class="fs28px fw-bold"><?=$service['name'];?></div>
                    <?php if (isset($service['fullName'])) { ?>
                        <div class="fs16px"><?=$service['fullName'];?></div>
                    <?php } ?>
                </div>
                <span class="btn-context"></span>
            </a>
        </div>
    <?php } ?>
</div>
