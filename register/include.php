<?php
/**
 * @var string $uLocale
 */
?>

<style>
    main {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        width: 100vw;
        height: 100vh;
        position: relative;
        background-color: #1F95EE !important;
    }

    footer {
        align-self: flex-end;
        width: 100%;
    }

    .align-self-bottom {
        align-self: flex-end;
    }
</style>

<script>
    $(() => {
        let step = 1;
        let data = null;
        let preId = null;
        let state = false;
        let setEmail = '';
        let setPhone = '';
        let setReferrer = '<?=($_GET['ref'] ?? '')?>';
        let formError = false;
        let smsRequestCount = 0;

        $(() => {
            $.ajaxSetup({
                beforeSend: function() {
                    preloaderOn();
                },
                complete: function(){
                    preloaderOff();
                }
            });

            $('#back').on('touchstart click', (e) => {
                e.stopImmediatePropagation();
                if (step > 1) {
                    if (e.type === 'touchstart') {
                        $(e.target).off('click');
                        buildStep((step-1), true);
                    }

                    if (e.type === 'click' ) {
                        buildStep((step-1), true);
                    }
                }
            });

            $('#next').on('touchstart click', (e) => {
                e.stopImmediatePropagation();
                if (step < 7) {
                    if (e.type === 'touchstart') {
                        $(e.target).off('click');
                        buildStep((step+1));
                    }

                    if (e.type === 'click') {
                        buildStep((step+1));
                    }
                }
            });

            $('.opacity')
                .removeClass('hidden')
                .addClass('shown');

            /*replaceInputBlock(6);
            currentStep(6);
            fieldsValidators();*/
        });

        const buildStep = (stepNumber, previous = false) => {
            $('#next').attr('disabled', true);
            $('#back').attr('disabled', true);
            displayProcessMessage('');
            console.log(`Running step ${stepNumber}`);

            if (previous) {
                if (stepNumber === 3 || stepNumber === 2) {
                    stepNumber = 1;
                } else if (stepNumber === 5 || stepNumber === 6) {
                    stepNumber = 4;
                }
            }

            switch (stepNumber) {
                case 1:
                    buildEmailBlock(stepNumber);
                    break;
                case 2:
                    buildEmailCodeBlock(stepNumber);
                    break;
                case 3:
                    buildReferrerBlock(stepNumber);
                    break;
                case 4:
                    buildPhoneBlock(stepNumber);
                    break;
                case 5:
                    buildPhoneCodeBlock(stepNumber)
                    break;
                case 6:
                    buildUserFieldBlock(stepNumber);
                    break;
                case 7:
                    completeRegistration(stepNumber);
                    break;
                default:
                    displayProcessMessage('<?=lang('something goes wrong', APP_LANG_SENTENCE)?>. <?=lang('please, try again', APP_LANG_SENTENCE)?>!');
                    break;
            }

            $('#next').removeAttr('disabled');
            $('#back').removeAttr('disabled');
        }

        const buildEmailBlock = (stepNumber) => {
            replaceInputBlock(stepNumber);
            currentStep(stepNumber);
            displayProcessMessage('');
            $('#register-from-gu').css('display', 'flex');
        };

        const buildEmailCodeBlock = (stepNumber) => {
            let email = $('input[name="email"]').val();
            if (!email) {
                email = setEmail;
            } else {
                setEmail = email;
            }

            $.post('/register/ajax/',{
                email: email,
                lang: '<?=$uLocale?>'
            }, response => {
                if (response.status === 'success') {
                    preId = response.reg_id;
                    state = true;
                    replaceInputBlock(stepNumber);
                    currentStep(stepNumber);
                    $('#register-from-gu').css('display', 'none');
                } else {
                    displayProcessMessage(response.message, response.status);
                }
                console.log(response);
            });
        }

        const buildReferrerBlock = (stepNumber) => {
            let emailCode = $('input[name="mailcode"]').val();

            $.post('/register/ajax/',{
                mailcode: emailCode,
                regid: preId,
                lang: '<?=$uLocale?>'
            }, response => {
                if (response.status === 'success') {
                    preId = response.reg_id;
                    state = true;
                    replaceInputBlock(stepNumber);
                    currentStep(stepNumber);
                } else {
                    displayProcessMessage(response.message, response.status);
                }
                console.log(response);
            });
        }

        const buildPhoneBlock = (stepNumber) => {
            let referrer = $('input[name="ref"]').val();

            if (!referrer) {
                referrer = setReferrer;
            } else {
                setReferrer = referrer;
            }

            $.post('/register/ajax/',{
                ref: referrer,
                regid: preId,
                lang: '<?=$uLocale?>'
            }, response => {
                if (response.status === 'success') {
                    preId = response.reg_id;
                    state = true;
                    replaceInputBlock(stepNumber);
                    currentStep(stepNumber);
                } else {
                    displayProcessMessage(response.message, response.status);
                }
                console.log(response);
            });
        }

        const buildPhoneCodeBlock = (stepNumber) => {
            let phone = $('input[name="phone"]').val();

            if (phone) {
                setPhone = phone;
            } else {
                phone = setPhone;
            }

            let data = {
                phone: phone,
                regid: preId,
                lang: '<?=$uLocale?>'
            };

            if (smsRequestCount > 0) {
                data.sms_trymore = true;
            }

            $.post('/register/ajax/', data, response => {
                smsRequestCount += 1;
                if (response.status === 'success') {
                    preId = response.reg_id;
                    state = true;
                    replaceInputBlock(stepNumber);
                    currentStep(stepNumber);
                } else {
                    displayProcessMessage(response.message, response.status);
                }
                console.log(response);
            });
        }

        const buildUserFieldBlock = (stepNumber) => {
            let phoneCode = $('input[name="phonecode"]').val();
            $.post('/register/ajax/',{
                phonecode: phoneCode,
                regid: preId,
                lang: '<?=$uLocale?>'
            }, response => {
                if (response.status === 'success') {
                    preId = response.reg_id;
                    state = true;
                    replaceInputBlock(stepNumber);
                    currentStep(stepNumber);
                    fieldsValidators();
                } else {
                    displayProcessMessage(response.message, response.status);
                }
                console.log(response);
            });
        }

        const completeRegistration = (stepNumber) => {
            let login = $('input[name="login"]').val();
            let password = $('input[name="password"]').val();
            let name = $('input[name="name"]').val();
            let language = $('input[name="language"]').val();
            let currency = $('input[name="currency"]').val();

            $.post('/register/ajax/',{
                login: login,
                name: name,
                password: password,
                language: language,
                currency: currency,
                regid: preId,
                lang: '<?=$uLocale?>'
            }, response => {
                if (response.status === 'success') {
                    preloaderOn();
                } else {
                    displayProcessMessage(response.message, response.status);
                }

                if (response.redirect) {
                    window.location.href = response.redirect;
                }
            });

        }

        const redirectTo = (url = '') => {
            if (url.length) {
                window.location.href = url;
            } else {
                window.location.href = '/';
            }
        }

        const displayProcessMessage = (message, status = 'success') => {
            let infoClass = status === 'success' ? 'default' : 'danger';
            $('#auth-info-message')
                .removeClass('text-danger text-default')
                .addClass(`text-${infoClass}`)
                .html(message);
        }

        const replaceInputBlock = (stepNumber) => {
            let fields = {
                1: {
                    type: "email",
                    name: "email",
                    placeholder: "<?=lang('your email', APP_LANG_SENTENCE)?>",
                    value: setEmail,
                    class: "",
                },
                2: {
                    type: "text",
                    name: "mailcode",
                    placeholder: "<?=lang('code from email', APP_LANG_SENTENCE)?>",
                    value: '',
                    class: "",
                },
                3: {
                    type: "text",
                    name: "ref",
                    placeholder: "<?=lang('inviter login', APP_LANG_SENTENCE)?>",
                    value: setReferrer,
                    class: "",
                },
                4: {
                    type: "text",
                    name: "phone",
                    placeholder: "<?=lang('your phone', APP_LANG_SENTENCE)?>",
                    value: setPhone,
                    class: "",
                },
                5: {
                    type: "text",
                    name: "phonecode",
                    placeholder: "<?=lang('code from sms', APP_LANG_SENTENCE)?>",
                    value: '',
                    class: "",
                },
                6: [
                    {
                        type: "text",
                        name: "login",
                        placeholder: "<?=lang('login', APP_LANG_SENTENCE)?>",
                        value: '',
                        class: "login-input",
                    },
                    {
                        type: "text",
                        name: "name",
                        placeholder: "<?=lang('full name', APP_LANG_SENTENCE)?>",
                        value: '',
                        class: "",
                    },
                    {
                        type: "password",
                        name: "password",
                        placeholder: "<?=lang('password', APP_LANG_SENTENCE)?>",
                        value: '',
                        class: "password-input",
                    },
                ]
            };

            let stepFields = fields[stepNumber];
            console.log(stepFields, stepNumber);

            $('.auth-input-block').html('');

            if (Array.isArray(stepFields)) {
                let content = '<div class="row">';
                stepFields.map((field) => {
                    content += `<div class="col-12 col-md-6">
<input type="${field.type}" name="${field.name}" id="${field.name}" class="form-control text-center text-black font-weight-bold f14px mb-4 ${field.class}" value="${field.value}" placeholder="${field.placeholder}">
</div>`;
                });

                content += `<div class="col-12 col-md-6">${getSelectors()}</div></div>`;

                $('.auth-input-block').append(content);
            } else {
                $('.auth-input-block')
                    .append(`<input type="${stepFields.type}" name="${stepFields.name}" id="${stepFields.name}" class="${stepFields.class}" value="${stepFields.value}" placeholder="${stepFields.placeholder}">`);
            }

            // inputBindings();
            setRequirement(stepNumber);
            // appendSelectors(stepNumber);

            if (stepNumber !== 6) {
                if ($('#password-validation-act')) {
                    $('#password-validation-act').remove();
                }

                if ($('.login-input-error')) {
                    $('.login-input-error').remove();
                }
            }

            if (state) {
                step = stepNumber;

                if (step > 1) {
                    $('#back').removeClass('d-none');
                } else {
                    $('#back').addClass('d-none');
                }
            }
        }

        const setRequirement = (stepIndex) => {
            let reqs = {
                1: {
                    text: "<?=lang('Enter your valid mail, a confirmation code will be sent to it')?>",
                },
                2: {
                    text: "<?=lang('A message was sent to your mail with a confirmation code, indicate it in the field below')?>",
                },
                3: {
                    text: "<?=lang('Enter the login of the user from whom you received invitations to join')?>",
                },
                4: {
                    text: "<?=lang('Enter your phone number, a message with a confirmation code will be sent to it')?>",
                },
                5: {
                    text: "<?=lang('Enter the code from the message to confirm the phone number')?>",
                },
                6: {
                    text: lastStepRequirements()
                }
            };

            if (reqs[stepIndex]) {
                $('.aib-content').html(reqs[stepIndex].text);
            }
        }

        const currentStep = (stepNumber) => {
            $('#stepState').html(stepNumber);
        }

        const fieldsValidators = () => {
            $('input.login-input').on('input', (e) => {
                let login = $(e.target).val();
                let className = 'login-input-error';

                $(e.target).removeClass('border-danger');

                if ($(`.${className}`)) {
                    $(`.${className}`).remove();
                    formError = false;
                }

                if (hasMultiLanguageLetters(login)) {
                    formError = true;
                    $(e.target).addClass('border-danger').after(inputAlert('<?=lang('allowed to use symbols of the same language layout', APP_LANG_SENTENCE)?>', className));
                }

                if (hasDeprecatedSymbols(login)) {
                    formError = true;
                    $(e.target)
                        .addClass('border-danger')
                        .after(inputAlert('<?=lang('only letters, numbers, dashes, and underscores can be used', APP_LANG_SENTENCE)?>', className));
                }
            });

            $('input.password-input').on('input', (e) => {
                let password = $(e.target).val();

                if (password.length < 7) {
                    $('#password-min-symbols').attr('src', '<?=SITE_TEMPLATE_PATH?>/images/app/check-fail.svg');
                } else {
                    $('#password-min-symbols').attr('src', '<?=SITE_TEMPLATE_PATH?>/images/app/check-success.svg');
                }

                if (/[0-9]/.test(password)) {
                    $('#password-digits').attr('src', '<?=SITE_TEMPLATE_PATH?>/images/app/check-success.svg');
                } else {
                    $('#password-digits').attr('src', '<?=SITE_TEMPLATE_PATH?>/images/app/check-fail.svg');
                }
            });
        }

        const hasMultiLanguageLetters = (inputString) => {
            let latin = /[a-zA-Z]/.test(inputString);
            let cyrillic = /[а-яА-ЯёЁ]/.test(inputString);

            return latin === true && cyrillic === true;
        }

        const hasDeprecatedSymbols = (inputString) => {
            return /[^a-zA-Zа-яА-ЯёЁ0-9_\-\.]+/.test(inputString);
        }

        const inputAlert = (message, className) => {
            return `<p class="${className} text-center f14px my-2 text-danger">${message}</p>`;
        }

        const passwordValidator = () => {
            if (!$('#password-validation-act').length) {
                $('#processItem').append(`
        <div class="my-3" id="password-validation-act">
<div class="d-flex justify-content-start pl-2 pl-sm-3 pl-md-5">
    <div class="font-weight-bold text-default f18px p-0">
        <i class="far fa-circle" id="password-7-symbols"></i>
    </div>
    <div class="d-flex align-items-center ml-3"><?=lang('minimum 7 characters', APP_LANG_SENTENCE)?></div>
</div>
<div class="d-flex justify-content-start pl-2 pl-sm-3 pl-md-5">
    <div class="font-weight-bold text-default f18px p-0">
        <i class="far fa-circle" id="password-digits"></i>
    </div>
    <div class="d-flex align-items-center ml-3"><?=lang('numbers')?></div>
</div>
</div>`);
            }
        }

        const preloaderOn = () => {
            $('#preloader').css('display', 'block');
        }

        const preloaderOff = () => {
            $('#preloader').css('display', 'none');
        }

        const inputBindings = () => {
            $('#processItem input')
                .off()
                .on('input', (e) => {
                    if ($(e.target).val() !== '') {
                        $(e.target).addClass('filled');
                    } else {
                        $(e.target).removeClass('filled');
                    }
                });
        }

        const appendSelectors = (stepNumber) => {
            let content = `<div class="row">
<div class="col-6">
<label for="language" class="mb-0"><?=lang('language', APP_LANG_TITLE)?></label>
<select name="language" id="language" class="form-control">
<option value="ru">Русский</option>
<option value="en">English</option>
</select>
</div>
<div class="col-6">
<label for="currency" class="mb-0"><?=lang('currency', APP_LANG_TITLE)?></label>
<select name="currency" id="currency" class="form-control">
<option value="rub">&#8381; Рубль</option>
<option value="usd">&#36; US Dollars</option>
<option value="eur">&#128; Euro</option>
</select>
</div>
</div>`;

            if ($('#selectors')) {
                $('#selectors').remove();
            }

            if (stepNumber === 6) {
                $('#processItem').append(`<div class="mt-3" id="selectors">${content}</div>`);
            }
        }
    });

    const getCurrencySelector = () => {
        let currencies = [
            {
                label: "<?=lang('ruble', APP_LANG_SENTENCE);?>",
                icon: "<?=SITE_TEMPLATE_PATH?>/images/app/ruble-icon.svg",
                id: 'rub',
            },
            {
                label: "<?=lang('dollar', APP_LANG_SENTENCE);?>",
                icon: "<?=SITE_TEMPLATE_PATH?>/images/app/dollar-icon.svg",
                id: 'usd',
            },
            {
                label: "<?=lang('euro', APP_LANG_SENTENCE);?>",
                icon: "<?=SITE_TEMPLATE_PATH?>/images/app/euro-icon.svg",
                id: 'eur',
            }
        ];
        let elements = '';
        currencies.map(crr => {
            elements += `<li class="dropdown-item" data-id="${crr.id}" onclick="chooseItem(this);">
                    <img src="${crr.icon}" class="currency-icon"> ${crr.label}
                </li>`
        });
        let currencyLabel = '<?=lang('currency', APP_LANG_SENTENCE)?>';
        return `<div class="auth-form-group position-relative">
<input type="hidden" name="currency" value=${currencies[0].id}>
<label for="currency">${currencyLabel}</label>
<div class="auth-select-box" onclick="toggleList(this);">
<img src="${currencies[0].icon}" class="currency-icon"> ${currencies[0].label}
<img src="<?=SITE_TEMPLATE_PATH?>/images/app/arrow-down.svg" class="auth-select-arrow">
</div>
<div class="auth-dropdown">
${elements}
</div>
</div>`;
    };

    const getLanguageSelector = () => {
        let languages = [
            {
                label: "<?=lang('russian', APP_LANG_SENTENCE);?>",
                icon: "<?=SITE_TEMPLATE_PATH?>/images/app/ruble-icon.svg",
                id: 'ru',
            },
            {
                label: "<?=lang('english', APP_LANG_SENTENCE);?>",
                icon: "<?=SITE_TEMPLATE_PATH?>/images/app/dollar-icon.svg",
                id: 'en',
            },
        ];
        let elements = '';
        languages.map(crr => {
            elements += `<li class="dropdown-item" data-id="${crr.id}" onclick="chooseItem(this);">
                    ${crr.label}
                </li>`
        });
        let languageLabel = '<?=lang('language', APP_LANG_SENTENCE)?>';
        return `<div class="auth-form-group position-relative">
<input type="hidden" name="language" value=${languages[0].id}>
<label for="language">${languageLabel}</label>
<div class="auth-select-box" onclick="toggleList(this);">
${languages[0].label}
<img src="<?=SITE_TEMPLATE_PATH?>/images/app/arrow-down.svg" class="auth-select-arrow">
</div>
<div class="auth-dropdown">
${elements}
</div>
</div>`;
    };

    const getSelectors = () => {
        let currencySelector = getCurrencySelector();
        let languageSelector = getLanguageSelector();

        return `<div class="d-flex align-items-center justify-content-between">
${languageSelector} ${currencySelector}
</div>`;
    }

    const toggleList = (element) => {
        let higher = $(element).closest('.auth-form-group');
        let list = higher.find('.auth-dropdown');
        let selector = higher.find('.auth-select-box');

        if (list.hasClass('opened')) {
            list.removeClass('opened');
            toggleSelectBox(selector);
        } else {
            list.addClass('opened');
            toggleSelectBox(selector, true);
        }
    }

    const chooseItem = (element) => {
        let value = $(element).data('id');
        $(element)
            .closest('.auth-form-group')
            .find('input[type="hidden"]')
            .val(value);
        let content = $(element).html();
        $(element)
            .closest('.auth-form-group')
            .find('.auth-select-box')
            .html(`
        ${content}
<img src="<?=SITE_TEMPLATE_PATH?>/images/app/arrow-down.svg" class="auth-select-arrow">
        `)
        toggleList(element);
    }

    const closeList = (element) => {
        let list = $(element)
            .closest('.auth-dropdown');

            list.removeClass('opened');
            toggleSelectBox(list.find('.auth-select-box'));
    }

    const toggleSelectBox = (element, open = false) => {
        if (open)
            $(element).addClass('opened');
        else
            $(element).removeClass('opened')
    }

    const lastStepRequirements = () => {
        return `<div>
<div class="fw-bold">Придумайте пароль</div>
<div class="d-flex justify-content-start align-items-center">
    <div class="auth-requirement-icon">
        <img src="<?=SITE_TEMPLATE_PATH?>/images/app/check-wait.svg" class="h-100" id="password-min-symbols">
    </div>
    <div class="auth-requirement-text">Минимум 7 символов</div>
</div>
<div class="d-flex justify-content-start align-items-center">
    <div class="auth-requirement-icon">
        <img src="<?=SITE_TEMPLATE_PATH?>/images/app/check-wait.svg" class="h-100" id="password-digits">
    </div>
    <div class="auth-requirement-text">Цифры</div>
</div>
</div>`;
    }
</script>
