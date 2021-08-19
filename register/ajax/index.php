<?php
/**
 * @var string $uLocale
 */

use Local\Api\Utils\Notification;

include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/ticgroup/ticprolog.php");

require_once $_SERVER['DOCUMENT_ROOT'] .'/modules/helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/local/api/utils/register.php';

$db = \Bitrix\Main\Application::getConnection('server');
$registerUtil = new \Local\Api\Utils\Register($db);

$preId = $_POST['regid'] ?? '';
$email = $_POST['email'] ?? '';
$mailCode = $_POST['mailcode'] ?? '';
$referrer = $_POST['ref'] ?? '';
$phone = $_POST['phone'] ?? '';
$phoneCode = $_POST['phonecode'] ?? '';
$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';
$name = $_POST['name'] ?? '';
$reply = $_POST['sms_trymore'] ?? '';
$partial = isset($_POST['partial']);
$needAuthSms = isset($_POST['need_sms']);
$finalAuthAct = isset($_POST['final_act']);

if (isset($_POST['lang'])) {
    $uLocale = $_POST['lang'];
}

// $isAgree = isset($_POST['agree']);

$registerUtil->setPartial($partial);
$registerUtil->setLocale($uLocale);

$validation = [];
if (!$registerUtil->isPartial()) {
    $validation = $registerUtil->validate($preId, $email, $login, $password, $name, $mailCode, $phone, $phoneCode, $referrer);
}

if (!empty($validation)) {
    json($validation);
}

if (!empty($email)) {
    $code = rand(100000, 999999);
    if ($registerUtil->emailIsExists($email)) {
        json([
            'status' => 'error',
            'message' => lang('The user with the specified mail already exists. If this is you, then try to enter your personal account')
        ]);
    }
    $update = false;

    if (!$partial) {
        $preId = $registerUtil->newPreRegistration($email, $code);
    } else {
        $preRegistration = $registerUtil->getPreRegistration($preId);
        if ($email !== $preRegistration['Mail']) {
            $update = $registerUtil->updateEmail($preRegistration, $email);

            if (!$update) {
                json([
                    'status' => 'error',
                    'message' => lang('failed to change email', APP_LANG_SENTENCE)
                ]);
            }
        }
    }

    $send = $registerUtil->sendEmail($email, $code, $preId);

    if ($update) {
        $send['new_mail'] = obfuscateEmail($email);
    }

    json($send);
}

if (empty($preId) && !$needAuthSms) {
    json([
        'status' => 'error',
        'message' => lang('offer not found', APP_LANG_SENTENCE)
    ]);
}

if (!empty($mailCode)) {
    if (!empty($referrer) || !empty($phone) || !empty($phoneCode) || (!empty($login) && !empty($password) && !empty($name))) {

    } else {
        $check = $registerUtil->checkEmailCode($preId, $mailCode);
        json($check);
    }
}

if (!empty($referrer)) {
    if (!empty($phone) || !empty($phoneCode) || (!empty($login) && !empty($password) && !empty($name))) {

    } else {
        $check = $registerUtil->checkReferrer($preId, $referrer);
        json($check);
    }
}

if (!empty($phone)) {
    if (!empty($phoneCode) || (!empty($login) && !empty($password) && !empty($name))) {

    } else {
        $update = false;
        if ($partial && !$needAuthSms) {
            $preRegistration = $registerUtil->getPreRegistration($preId);
            if (preg_replace('/[^0-9]/', '', $preRegistration['Phone']) !== preg_replace('/[^0-9]/', '', $phone)) {
                $update = $registerUtil->updatePhone($preRegistration, $phone);

                if (!$update) {
                    json([
                        'status' => 'error',
                        'message' => lang('failed to change phone', APP_LANG_SENTENCE)
                    ]);
                }
            }
        }

        if ($needAuthSms) {
            $registerUtil->setLoginOperation(true);
            $registerUtil->setApplication('web');
            $user = CUser::GetByLogin($login)->Fetch();
            $registerUtil->setUser($user);
        }

        $send = $registerUtil->sendSms($preId, $phone, $_SERVER['REMOTE_ADDR'] ?? '', $reply);

        if ($update) {
            $send['new_phone'] = obfuscatePhone($phone);
        }

        json($send);
    }
}

if (!empty($phoneCode)) {
    if (!empty($login) && !empty($password) && !empty($name)) {

    } else {
        if ($needAuthSms) {
            $registerUtil->setLoginOperation(true);
            $registerUtil->setApplication('web');
            $user = CUser::GetByLogin($login)->Fetch();
            $registerUtil->setUser($user);
        }

        $check = $registerUtil->checkSms($preId, $phoneCode, $_SERVER['REMOTE_ADDR'] ?? '');
        json($check);
    }
}

if ($needAuthSms && !empty($login) && $finalAuthAct) {
    $user= CUser::GetByLogin($login)->Fetch();
    $preLogin = getLastPreLogin($user['ID']);
    $confirmation = getSmsConfirmsByCode($user['ID'], $preLogin['Phone'], $preLogin['Message']);

    if ($confirmation) {
        json([
            'status' => 'error',
            'message' => lang('invalid confirmation code', APP_LANG_SENTENCE, $uLocale),
            'field' => 'phone'
        ]);
    }

    json([
        'status' => 'success',
        'message' => lang('you have successfully authorized', APP_LANG_SENTENCE) . '. '
            . lang('go to your personal account to continue working in the system', APP_LANG_SENTENCE),
        'redirect' =>'reload'
    ]);
}

if (!empty($login) && !empty($password) && !empty($name)) {
    global $USER;
    Logger::debug($_POST);
    $preRegistration = $registerUtil->getPreRegistration($preId);

    if ($preRegistration['MailVerified'] != 1 || $preRegistration['PhoneVerified'] != 1) {
        json([
            'status' => 'error',
            'message' => lang('be sure to confirm your email and phone number', APP_LANG_SENTENCE),
            'field' => $preRegistration['MailVerified'] != 1 ? 'mail' : ($preRegistration['PhoneVerified'] != 1 ? 'phone' : '')
        ]);
    }

    $appUrl = getAppUrl();

    if ($USER->IsAuthorized()) {
        json([
            'status' => 'error',
            'message' => lang('you authorized', APP_LANG_SENTENCE),
            'redirect' => "{$appUrl}"
        ]);
    }

    if (!isset($_POST['language']) || empty($_POST['language'])) {
        json([
            'status' => 'error',
            'message' => lang('choose language', APP_LANG_SENTENCE)
        ]);
    }

    $language = $_POST['language'];

    if (!isset($_POST['currency']) || empty($_POST['currency'])) {
        json([
            'status' => 'error',
            'message' => lang('choose main currency', APP_LANG_SENTENCE)
        ]);
    }

    $currency = $_POST['currency'];

    $_cfg['Const_Salt'] = '7c908adea6';

    require($_SERVER["DOCUMENT_ROOT"]."/oldmoduleslibs/sutils.php");
    require($_SERVER["DOCUMENT_ROOT"]."/oldmoduleslibs/main56.php");
    require($_SERVER["DOCUMENT_ROOT"]."/oldmoduleslibs/tplutils56.php");
    require($_SERVER["DOCUMENT_ROOT"]."/oldmoduleslibs/lib.php");
    require($_SERVER["DOCUMENT_ROOT"]."/oldmoduleslibs/depolib.php");
    require($_SERVER["DOCUMENT_ROOT"]."/oldmoduleslibs/balancelib.php");
    require($_SERVER["DOCUMENT_ROOT"]."/oldmoduleslibs/libdb.php");

    $connections = \Bitrix\Main\Config\Configuration::getValue('connections');
    $srvConnConf = $connections['server'];

    $db = new HS2_DB();

    $db->open(
        $srvConnConf['host'],
        $srvConnConf['database'],
        $srvConnConf['login'],
        $srvConnConf['password']
    );


    $precheck = true;

    $check1 = preg_match("/[a-zA-Z]+/",$login);
    $check2 = preg_match("/[а-яА-ЯёЁ]+/",$login);

    if ($check1 && $check2)
    {
        json([
            'status' => 'error',
            'message' => lang('allowed to use symbols of the same language layout')
        ]);

        $precheck = false;
    }

    if (preg_match("/[^а-яА-ЯёЁa-zA-Z0-9\-_]+/", $login)) {
        json([
            'status' => 'error',
            'message' => lang('only letters, numbers, dashes, and underscores can be used')
        ]);

        $precheck = false;
    }

    $preRegistration = $registerUtil->getPreRegistration($preId);

    if (isset($preRegistration['status']) && $preRegistration['status'] == 'error') {
        json($preRegistration);
    }

    $referrer = getUserByID($preRegistration['From_Ref']);

    if (empty($referrer)) {
        json([
            'status' => 'error',
            'message' => lang('inviter not found', APP_LANG_SENTENCE)
        ]);
    }

    if ($precheck) {
        $params['uMail'] = str_replace(' ', '', $preRegistration['Mail']);
        $params['uLogin'] = $login;
        $params['uPass'] = $password;
        $params['Pass2'] = $password;
        $params['aTel'] = $preRegistration['Phone'];
        $params['aName'] = $name;
        $params['Agree'] = true;
        $params['uRef'] = $referrer['uLogin'];

        $resultreg = opRegisterPrepare($params);
    }

    if ($resultreg == 'mail_used') {
        json([
            'status' => 'error',
            'message' => lang('This email address is already in use. It is forbidden to register two accounts for one mail.')
        ]);
    } else if ($resultreg == 'tel_wrong') {
        json([
            'status' => 'error',
            'message' => lang('A phone number can only consist of numbers and signs +, (,)')
        ]);
    } else if ($resultreg == 'login_used') {
        json([
            'status' => 'error',
            'message' => lang('Login is not available for registration. Choose another one.')
        ]);
    } else if ($resultreg == 'ref_not_found') {
        json([
            'status' => 'error',
            'message' => lang('inviter not found', APP_LANG_SENTENCE)
        ]);
    } else if ($resultreg == 'pass_not_equal') {
        json([
            'status' => 'error',
            'message' => lang('Password and password confirmation do not match.')
        ]);
    } else if ($resultreg == 'ref_disabled') {
        json([
            'status' => 'error',
            'message' => lang('registration with this user is limited', APP_LANG_SENTENCE) . '. '
                . lang('contact support for information', APP_LANG_SENTENCE) . '.'
        ]);
    }  else if ($resultreg == 'wrong_db_connection') {
        json([
            'status' => 'error',
            'message' => lang('failed to register', APP_LANG_SENTENCE) . '. '
                . lang('please, try later', APP_LANG_SENTENCE) . '.'
        ]);
    }   else if ($resultreg == 'non_creation') {
        json([
            'status' => 'error',
            'message' => lang('failed to register', APP_LANG_SENTENCE) . '. '
                . lang('please, try later', APP_LANG_SENTENCE) . '.'
        ]);
    } else {
        if (is_numeric($resultreg)) {
            if ($resultreg != 1) {

                $db->update('Users', [
                    'uLang' => $language,
                    'uMainCurr' => mb_strtoupper($currency),
                    'AppID' => 's2'
                ], '',  "uID=?d", [$resultreg]);

                $registerUtil->updatePreRegistration($preRegistration, [
                    'created_user_id' => $resultreg,
                    'last_update_date' => gmdate('Y-m-d H:i:s'),
                    'name' => $name,
                    'login' => $login,
                    'password' => ''
                ]);

                require_once $_SERVER['DOCUMENT_ROOT'] . '/local/api/utils/notification.php';

                $noteUtil = new Notification();
                $noteUtil->setPartnerId($resultreg);
                $noteUtil->setGroup(Notification::NOTIFICATION_GROUP_PARTNER);
                $noteUtil->setType(Notification::NOTIFICATION_TYPE_PARTNER_NEW);
                try {
                    $note = $noteUtil->new();
                } catch (\Throwable $exception) {
                    journal()->add('request-response', [
                        'request' => request()->get(),
                        'response' => [
                            'method' => 'opRegisterPrepare',
                            'error' => $exception->getMessage(),
                            'trace' => $exception->getTraceAsString()
                        ]
                    ]);
                }

                $USER->Authorize($resultreg);
                json([
                    'status' => 'success',
                    'message' => lang('you have successfully registered', APP_LANG_SENTENCE) . '. '
                        . lang('go to your personal account to continue working in the system', APP_LANG_SENTENCE),
                    'redirect' => $appUrl
                ]);
            }
        } else {
            if ($resultreg != ''){
                json([
                    'status' => 'error',
                    'message' => $resultreg,
                ]);
            }
        }
    }
}

json([
    'status' => 'error',
    'message' => lang('incorrect registration sequence', APP_LANG_SENTENCE) . '. '
        . lang('please go back or start over', APP_LANG_SENTENCE) . '.'
]);

function opRegisterPrepare($params, $from_admin = false)
{
    $saverealpass = $params['uPass'];
    $res = opRegisterUserCheck($params, 0, $from_admin);
    if ($res !== true)
        return $res;
    if (!$from_admin and !$params['Agree'])
        return 'must_agree';

    global $_GS, $db, $_cfg;
    if (!$from_admin and $_cfg['Account_RegCheck'])
    {
        if ($_cfg['Account_RegCheck'] & 1)
            if ($db->count('AddInfo', 'aCIP=?', array($_GS['client_ip'])))
                return 'multi_reg';
        if ($_cfg['Account_RegCheck'] & 2)
            if (_COOKIE('active'))
                return 'multi_reg';
    }
    $params['uState'] = 1;
    $params['uLevel'] = 1;
    $params['uLang'] = $_GS['lang'];
    $params['uMode'] = $_GS['mode'];
    $params['uTheme'] = $_GS['theme'];

    if ($params['uRef'] == 326) {
        $params['uWDDisable'] = 1;
        $params['uBal'] = 1000;
    } else {
        $params['uWDDisable'] = 0;
        $params['uBal'] = 0;
    }


    if ($params['uRef'] == 6140) {
        return 'ref_disabled';
    }

    $connections = \Bitrix\Main\Config\Configuration::getValue('connections');

    $btxConnConf = $connections['default'];


    //дубляж в НDB
    $mysqliBpers2020 = new mysqli(
        $btxConnConf['host'],
        $btxConnConf['login'],
        $btxConnConf['password'],
        $btxConnConf['database']
    );

    mysqli_set_charset($mysqliBpers2020, 'utf8');
    if ($mysqliBpers2020->connect_errno) {
        return 'wrong_db_connection';
    }


    $params['auID'] = $db->insert('Users', $params,
        'uLogin, uPass, uMail, uState, uLevel, uLang, uMode, uTheme, uRef, uWDDisable, uBal');


    $mysqliBpers2020->query("INSERT INTO `b_user` (`ID`, `LOGIN`, `EMAIL`, `PASSWORD`) VALUES (".$params['auID'].", '".$params['uLogin']."', '".$params['uMail']."', '".md5($saverealpass)."')");

    global $USER;
    $strError = '';

    $user = new CUser;
    $arFields = Array(
        "EMAIL"             => str_replace(' ', '', $params['uMail']),
        "LOGIN"             => str_replace(' ', '', $params['uLogin']),
        "NAME"              => $params['aName'],
        "PERSONAL_PHONE"    => $params['aTel'],
        "LID"               => "ru",
        "ACTIVE"            => "Y",
        "GROUP_ID"          => array(3,4),
        "PASSWORD"          => $saverealpass,
        "CONFIRM_PASSWORD"  => $saverealpass,
        "XML_ID"  => $params['auID']
    );
    $user->Update($params['auID'], $arFields);
    $strError .= $user->LAST_ERROR;

    if (!empty($strError)) {
        return $strError;
    }

    if ($params['uRef'] == 326) {
        /*print_r($params);
        die();*/

        $set1k['wcID'] = 2;
        $set1k['wuID'] = $params['auID'];
        $set1k['wBal'] = 1000;

        $db->insert('Wallets', $set1k, 'wcID, wuID, wBal');

        $oper1k['oCTS'] = date("YmdHis");
        $oper1k['oATS'] = date("YmdHis");
        $oper1k['oTS'] = date("YmdHis");
        $oper1k['oNTS'] = date("YmdHis");
        $oper1k['oMemo'] = 'Начисление бонуса за регистрацию по приглашению Chase';
        $oper1k['oOper'] = 'CALCIN';
        $oper1k['oState'] = 3;
        $oper1k['ouID'] = $params['auID'];
        $oper1k['ocID'] = 2;
        $oper1k['oSum'] = 1000;

        $db->insert('Opers', $oper1k, 'oCTS, oATS, oTS, oNTS, oMemo, oOper, oState, ocID, oSum, ouID');
    }

    if ($params['uRef'] == 6140) {
        return 'ref_disabled';
    }

    if (!$params['auID'])
        return 'non_creation';
    $params['aCTS'] = timeToStamp();
    $params['aCIP'] = $_GS['client_ip'];
    $db->insert('AddInfo', $params, 'auID, aName, aCTS, aCIP, aSQuestion, aSAnswer, aCountry, aTel');
    if (!$from_admin)
    {
        setcookie('active', $params['auID'], time() + 365 * HS2_UNIX_DAY, '/'); // mark 'registered'
        $params['uID'] = $params['auID'];
        SendMailToAdmin(
            'NewUser',
            opUserConsts($params)
        );
    }
    return $params['auID'];
}

function opRegisterUserCheck(&$params, $uid = 0, $from_admin = false) // !!! Pass2 must be set
{
    global $db, $_cfg;
    if (!$from_admin and ($_cfg['Account_UseName'] == 1) and (sEmpty($params['aName'])))
        return 'name_empty';
    if (!$_cfg['Const_NoLogins'])
    {
        if (sEmpty($params['uLogin']))
            return 'login_empty';
        if (($_cfg['Account_MinLogin'] > 0) and (strlen($params['uLogin']) < $_cfg['Account_MinLogin']))
            return 'login_short';
        if ($_cfg['Account_LoginRegx'] and !preg_match(exValue('/[^\s]+/', $_cfg['Account_LoginRegx']), $params['uLogin']))
            return 'login_wrong';
        if ($db->count('Users', 'uID<>?d and uLogin=?', array($uid, $params['uLogin'])) > 0)
            return 'login_used';
    }
    if (sEmpty($params['uMail']))
        return 'mail_empty';
    if (!validMail($params['uMail']))
        return 'mail_wrong';
    if ($db->count('Users', 'uID<>?d and uMail=?', array($uid, $params['uMail'])) > 0)
        return 'mail_used';
    if (sEmpty($params['uLogin']) or $_cfg['Const_NoLogins'])
        $params['uLogin'] = $params['uMail'];
    if (sEmpty($params['aName']))
        $params['aName'] = get1ElemL($params['uLogin'], '@');
    if (!$uid or !sEmpty($params['uPass']))
    {
        if (sEmpty($params['uPass']))
            return 'pass_empty';
        if (($_cfg['Account_MinPass'] > 0) and (strlen($params['uPass']) < $_cfg['Account_MinPass']))
            return 'pass_short';
        if ($_cfg['Account_PassRegx'] and !preg_match($_cfg['Account_PassRegx'], $params['uPass']))
            return 'pass_wrong';
        if (!$from_admin and !$uid and ($params['Pass2'] != $params['uPass']))
            return 'pass_not_equal';

        $params['uPass'] = md5($_cfg['Const_Salt'] . $params['uPass']);
        $params['uPTS'] = timeToStamp();
    }
    else
    {
        unset($params['uPass']);
        unset($params['uPTS']);
    }
    if ($uid) // from admin
    {
        if (!sEmpty($params['uPIN']))
        {
            if (($_cfg['Sec_MinPIN'] > 0) and (strlen($params['uPIN']) < $_cfg['Sec_MinPIN']))
                return 'pin_short';
            $params['uPIN'] = md5($params['uPIN'] . $_cfg['Const_Salt']);
        }
        else
            unset($params['uPIN']);
    }
    if (!$from_admin)
    {
        //if ($_cfg['SMS_REG'])
        if (1 == 1)
        {
            $params['aTel'] = preg_replace('|[^\d]|', '', $params['aTel']);
            if (textLen($params['aTel']) < 11)
                return 'tel_wrong';
        }
    }
    if ((($_cfg['Account_RegMode'] == 2) and !$params['uRef']) and (!$from_admin))
        return 'ref_empty';
    if ((!$from_admin or $uid) and $params['uRef'])
    {
        $ruid = $db->fetch1($db->select('Users', 'uID', 'uLogin=?', array($params['uRef'])));
        if (!$ruid)
            return 'ref_not_found';
        if ($uid and ($ruid == $uid))
            return 'ref_is_self';
    }
    else
        $ruid = 0;
    $params['uRef'] = $ruid;
    if (!$uid) // from reg
    {
        if (($_cfg['Account_RegMode'] == 3) and sEmpty($params['Invite']))
            return 'inv_empty';
        // ??? check Invite
    }
    if (($_cfg['Sec_MinSQA'] > 0) and (!$from_admin))
    {
        if (!$params['aSQuestion'])
            return 'secq_empty';
        if (strlen($params['aSQuestion']) < $_cfg['Sec_MinSQA'])
            return 'secq_short';
        if (!$uid and sEmpty($params['aSAnswer']))
            return 'seca_empty';
        if (!sEmpty($params['aSAnswer']))
        {
            if (strlen($params['aSAnswer']) < $_cfg['Sec_MinSQA'])
                return 'seca_short';
            if ($params['aSAnswer'] == $params['aSQuestion'])
                return 'seqa_equal_secq';
            $params['aSAnswer'] = md5($params['aSAnswer'] . $_cfg['Const_Salt']);
        }
        else
            unset($params['aSAnswer']);
    }
    return true;
}

function verifyReCapCha()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {

        // Build POST request
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_secret = '6Lcqdq4ZAAAAAOeEoe63fRCP09lz9OAG_rMgoBqJ';
        $recaptcha_response = $_POST['recaptcha_response'];

        // Make and decode POST request
        $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
        $recaptcha = json_decode($recaptcha);

        // Take action based on the score returned
        if ($recaptcha->score >= 0.7) {
            return true;
        } else {
            return false;
        }
    }
}

function getUserByID($userId)
{
    $db = \Bitrix\Main\Application::getConnection('server');
    $q = "select uLogin from Users where uID = {$userId}";
    $ex = $db->query($q);

    return $ex->fetchRaw() ?? [];
}

function getLastPreLogin($userId)
{
    global $db;
    $q = "select * from Last2faLogin where uID = {$userId} and Application = 'web' order by id desc limit 1";
    $ex = $db->query($q);

    return $ex->fetchRaw();
}

function getSmsConfirmsByCode($userId, $phone, $code)
{
    global $db;
    $q = "select * from SmsConfirms where id = {$userId} and Phone = '{$phone}' and Text = '{$code}' order by LastTry desc limit 1";
    Logger::debug($q);
    $ex = $db->query($q);

    return $ex->fetchRaw();
}

