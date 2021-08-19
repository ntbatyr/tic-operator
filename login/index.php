<?php
/**
 * @const SITE_TEMPLATE_PATH = ''
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$uLocale = 'ru';
if (isset($_GET['en']))
    $uLocale = 'en';
$wrongField = '';
$errorMessage = '';
if (!empty($_POST)) {
    if (empty($_POST['login'])) {
        $wrongField = 'login';
        $errorMessage = lang('login can not be empty', APP_LANG_SENTENCE);
    } else if (empty($_POST['password']) && empty($wrongField)) {
        $wrongField = 'password';
        $errorMessage = lang('password can not be empty', APP_LANG_SENTENCE);
    } else if (empty($wrongField) && empty($errorMessage)) {
        $user = CUser::GetByLogin($_POST['login'])->Fetch();

        if (empty($user) || empty($user['ID'])) {
            $wrongField = 'login';
            $errorMessage = lang('user with provided login not found', APP_LANG_SENTENCE);
        }

        if (empty($wrongField) && !\Bitrix\Main\Security\Password::equals($user['PASSWORD'], $_POST['password'])) {
            $wrongField = 'password';
            $errorMessage = lang('password does not match', APP_LANG_SENTENCE);
        }

        if (empty($wrongField) && empty($errorMessage)) {
            global $USER;
            $USER->Authorize($user['ID']);
            header("Location: /");
            die();
        }
    }
}

?>
<div class="mb-5 d-flex justify-content-center w-100">
    <div class="auth-logo-block">
        <img src="<?=SITE_TEMPLATE_PATH?>/images/app/logo-white.svg" class="w-100">
    </div>
</div>
<div class="row justify-content-center align-items-center">
    <div class="col-11 col-sm-10 col-md-11 col-lg-9 p-0 p-md-2">
        <form method="POST" action="/login/" class="card-body bg-white p-2 p-md-5 rounded-25px min-w-300">
            <div class="row">
                <div class="col-12 col-md-6 d-flex d-md-block mb-5 mb-md-0 flex-nowrap justify-content-between align-items-end">
                    <div class="fs35px fw-bold .self-align-start"><?=lang('authorize', APP_LANG_SENTENCE);?></div>

                    <div class="fs16px fw-bold .self-align-bottom">Шаг <span id="stepState">1</span> из 6</div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="auth-info-block">
                        <div class="aib-icon">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/app/info.svg" class="w-100">
                        </div>
                        <div class="aib-content">
                            Введите ваши логин и пароль для входа в кабинет.
                        </div>
                    </div>
                </div>
            </div>

            <div class="auth-input-block mt-5">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <input type="text" name="login" id="login"
                               class="form-control text-center text-black font-weight-bold f14px mb-4 <?=$wrongField === 'login' ? 'error' : ''?>"
                               value="<?=$_POST['login'] ?? ''; ?>" placeholder="<?=lang('login', APP_LANG_SENTENCE)?>"
                               autocomplete="login" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <input type="password" name="password" id="password"
                               class="form-control text-center text-black font-weight-bold f14px mb-4 <?=$wrongField === 'password' ? 'error' : ''?>"
                               placeholder="<?=lang('password', APP_LANG_SENTENCE)?>" required>
                    </div>
                </div>
            </div>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">
                    <?=lang('remember me on this device', APP_LANG_SENTENCE);?>
                </label>
            </div>

            <div id="auth-info-message" class="text-center fs16px my-2 text-danger"><?=$errorMessage;?></div>

            <div class="d-flex mt-5 justify-content-center flex-wrap">
                <button type="submit" class="btn btn-auth-primary"><?=lang('authorize', APP_LANG_SENTENCE);?></button>
            </div>

            <div class="mt-5 gu-reg" id="login-from-gu">
                <span><?=lang('Login through the portal Gosuslugi')?></span>
                <img src="<?=SITE_TEMPLATE_PATH?>/images/app/gosuslugi.svg" class="gu-logo ms-0 ms-md-3 mt-3 mt-md-0">
            </div>
        </form>
    </div>
</div>


<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>
