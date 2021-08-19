<?php
/**
 * @const SITE_TEMPLATE_PATH = ''
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$uLocale = 'ru';
if (isset($_GET['en']))
    $uLocale = 'en';

?>
<div class="mb-5 d-flex justify-content-center w-100">
    <div class="auth-logo-block">
        <img src="<?=SITE_TEMPLATE_PATH?>/images/app/logo-white.svg" class="w-100">
    </div>
</div>
<div class="row justify-content-center align-items-center">
    <div class="col-11 col-sm-10 col-md-11 col-lg-9 p-0 p-md-2">
        <div class="card-body bg-white p-2 p-md-5 rounded-25px min-w-300">
            <div class="row">
                <div class="col-12 col-md-6 d-flex d-md-block mb-5 mb-md-0 flex-nowrap justify-content-between align-items-end">
                    <div class="fs35px fw-bold .self-align-start"><?=lang('registration', APP_LANG_SENTENCE);?></div>

                    <div class="fs16px fw-bold .self-align-bottom">Шаг <span id="stepState">1</span> из 6</div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="auth-info-block">
                        <div class="aib-icon">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/app/info.svg" class="w-100">
                        </div>
                        <div class="aib-content">
                            Укажите Вашу действующую почту, на неё будет отправлен код подтверждения
                        </div>
                    </div>
                </div>
            </div>

            <div class="auth-input-block mt-5">
                <input type="email" name="email" placeholder="Ваша почта" autocomplete="off">
            </div>

            <div id="auth-info-message" class="text-center fs16px my-2"></div>

            <div class="d-flex mt-5 justify-content-center flex-wrap">
                <button type="button" class="btn btn-auth-primary d-none me-sm-3" id="back">Назад</button>
                <button type="button" class="btn btn-auth-primary" id="next">Далее</button>
            </div>

            <div class="mt-5 gu-reg" id="register-from-gu">
                <span><?=lang('Register through the portal Gosuslugi')?></span>
                <img src="<?=SITE_TEMPLATE_PATH?>/images/app/gosuslugi.svg" class="gu-logo ms-0 ms-md-3 mt-3 mt-md-0">
            </div>
        </div>
    </div>
</div>


<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>
