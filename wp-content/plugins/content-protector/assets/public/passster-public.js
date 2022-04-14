jQuery(document).ready(function ($) {

    // Check if we have an unlock link.
    if (ps_ajax.link_pass) {
        if ('on' === ps_ajax.use_cookie) {
            Cookies.set('passster', ps_ajax.link_pass, { expires: parseInt(ps_ajax.days) });
            window.location.replace(ps_ajax.permalink);
        }
    }

    // Passwords
    $('.passster-submit').on('click', function (e) {
        e.preventDefault();

        // Validate form before submitting ajax.
        var form = $(this).parent().parent();

        if (!$(form)[0].checkValidity()) {
            $(form)[0].reportValidity();
        }

        ps_id = $(this).attr('data-psid');
        form = $("#" + ps_id);
        password = $("#" + ps_id + ' .passster-password').attr('data-password');
        type = $("#" + ps_id + ' .passster-password').attr('data-protection-type');
        list = $("#" + ps_id + ' .passster-password').attr('data-list');
        area = $("#" + ps_id + ' .passster-password').attr('data-area');
        protection = $("#" + ps_id + ' .passster-password').attr('data-protection');
        redirect = $(this).attr('data-redirect');
        input = $("#" + ps_id + ' .passster-password').val();
        acf = $(this).attr('data-acf');

        $.ajax({
            type: "post",
            dataType: "json",
            url: ps_ajax.ajax_url,
            data: { 'action': 'validate_input', 'nonce': ps_ajax.nonce, 'input': input, 'password': password, 'post_id': ps_ajax.post_id, 'type': type, 'list': list, 'area': area, 'protection': protection, 'acf': acf, 'redirect': redirect },
            beforeSend: function () {
                form.find(".ps-loader").css('display', 'block');
            },
            success: function (response) {
                form.find(".ps-loader").css('display', 'none');
                if (true === response.success) {

                    // Redirect?
                    if (response.redirect) {
                        window.location.replace(redirect);
                    }

                    // if no ajax.
                    if ('on' === ps_ajax.no_ajax) {
                        console.log( ps_ajax.disable_encryption );

                        if ( ps_ajax.disable_encryption ) {
                            Cookies.set('passster', input, { expires: parseInt(ps_ajax.days) });
                        } else {
                            Cookies.set('passster', btoa(input), { expires: parseInt(ps_ajax.days) });
                        }
                       
                        window.location.reload();
                    } else {
                        // set cookie if activated.
                        if ('on' === ps_ajax.use_cookie) {
                            if ( ps_ajax.disable_encryption ) {
                                Cookies.set('passster', input, { expires: parseInt(ps_ajax.days) });
                            } else {
                                Cookies.set('passster', btoa(input), { expires: parseInt(ps_ajax.days) });
                            }
                        }
                        form.find('.passster-error').hide();

                        // replace shortcodes.
                        let content = response.content;

                        $.each(ps_ajax.shortcodes, function (key, value) {
                            content = content.replace(key, value);
                        });

                        $("#" + ps_id).replaceWith(content);
                    }
                } else {
                    form.find('.passster-error').text(response.error);
                    form.find('.passster-error').show().fadeOut(2500);
                    $("#" + ps_id + ' .passster-password').val('');
                }
            }
        });
    });

    // Recaptcha v2
    if ($('.recaptcha-form-v2').length > 0) {
        grecaptcha.ready(function () {
            grecaptcha.render('ps-recaptcha-v2', {
                'sitekey': ps_ajax.recaptcha_key,
                'callback': function (token) {
                    ps_id = $('.recaptcha-v2-submit').attr('data-psid');
                    form = $("#" + ps_id);
                    protection = $('.recaptcha-v2-submit').attr('data-protection');
                    acf = $('.recaptcha-v2-submit').attr('data-acf');
                    area = $("#" + ps_id).find('.recaptcha-v2-submit').attr('data-area');
                    redirect = $("#" + ps_id).find('.recaptcha-v2-submit').attr('data-redirect');

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: ps_ajax.ajax_url,
                        data: { 'action': 'validate_input', 'nonce': ps_ajax.nonce, 'token': token, 'post_id': ps_ajax.post_id, 'type': 'recaptcha', 'protection': protection, 'captcha_id': ps_id, 'acf': acf, 'area': area, 'redirect': redirect },
                        success: function (response) {
                            // Redirect?
                            if (response.redirect) {
                                window.location.replace(redirect);
                            }
                            // todo: set cookie if activated.
                            if (true === response.success) {
                                // if no ajax.
                                if ('on' === ps_ajax.no_ajax) {
                                    Cookies.set('passster', btoa('recaptcha'), { expires: parseInt(ps_ajax.days) });
                                    window.location.reload();
                                } else {
                                    // set cookie if activated.
                                    if ('on' === ps_ajax.use_cookie) {
                                        Cookies.set('passster', btoa('recaptcha'), { expires: parseInt(ps_ajax.days) });
                                    }
                                    form.find('.passster-error').hide();

                                    // replace shortcodes.
                                    let content = response.content;

                                    $.each(ps_ajax.shortcodes, function (key, value) {
                                        content = content.replace(key, value);
                                    });

                                    ("#" + ps_id).replaceWith(content);
                                }
                            } else {
                                form.find('.passster-error').text(response.error);
                                form.find('.passster-error').show().fadeOut(2500);
                            }
                        }
                    });
                }
            });
        });
    }

    // ReCaptcha v3
    $('.recaptcha-form').on( 'submit', function (event) {
        event.preventDefault();

        ps_id = $(this).find('.passster-submit-recaptcha').attr('data-psid');
        form = $("#" + ps_id);
        protection = $(this).find('.passster-submit-recaptcha').attr('data-protection');
        acf = $(this).find('.passster-submit-recaptcha').attr('data-acf');
        area = $(this).find('.passster-submit-recaptcha').attr('data-area');
        redirect = $(this).find('.passster-submit-recaptcha').attr('data-redirect');

        grecaptcha.ready(function () {
            grecaptcha.execute(ps_ajax.recaptcha_key, { action: 'validate_input' }).then(function (token) {

                form.prepend('<input type="hidden" name="token" value="' + token + '">');
                form.prepend('<input type="hidden" name="action" value="validate_input">');

                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: ps_ajax.ajax_url,
                    data: { 'action': 'validate_input', 'nonce': ps_ajax.nonce, 'token': token, 'post_id': ps_ajax.post_id, 'type': 'recaptcha', 'protection': protection, 'captcha_id': ps_id, 'acf': acf, 'area': area, 'redirect': redirect },
                    success: function (response) {
                        // todo: set cookie if activated.
                        if (true === response.success) {
                            // Redirect?
                            if (response.redirect) {
                                window.location.replace(redirect);
                            }
                            // if no ajax.
                            if ('on' === ps_ajax.no_ajax) {
                                Cookies.set('passster', btoa('recaptcha'), { expires: parseInt(ps_ajax.days) });
                                window.location.reload();
                            } else {
                                // set cookie if activated.
                                if ('on' === ps_ajax.use_cookie) {
                                    Cookies.set('passster', btoa('recaptcha'), { expires: parseInt(ps_ajax.days) });
                                }
                                form.find('.passster-error').hide();
                                // replace shortcodes.
                                let content = response.content;

                                $.each(ps_ajax.shortcodes, function (key, value) {
                                    content = content.replace(key, value);
                                });

                                form.replaceWith(content);
                            }
                        } else {
                            form.find('.passster-error').text(response.error);
                            form.find('.passster-error').show().fadeOut(2500);
                        }
                    }
                });
            });
        });
    });

    // Captcha
    if ($('.passster-captcha-input').length > 0) {
        var captcha = new jCaptcha({
            el: '.passster-captcha-input',
            canvasClass: 'jCaptchaCanvas',
            canvasStyle: {
                // properties for captcha stylings
                width: 100,
                height: 25,
                textBaseline: 'top',
                font: '22px Arial',
                textAlign: 'left',
                fillStyle: '#000',
            },
            'requiredValue': '',
            callback: function (response) {
                if (response == 'success') {
                    ps_id = $('.passster-submit-captcha').attr('data-psid');
                    form = $("#" + ps_id);
                    protection = $('.passster-submit-captcha').attr('data-protection');
                    acf = $('.passster-submit-captcha').attr('data-acf');
                    area = $("#" + ps_id).find('.passster-captcha-input').attr('data-area');
                    redirect = $('.passster-submit-captcha').attr('data-redirect');

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: ps_ajax.ajax_url,
                        data: { 'action': 'validate_input', 'nonce': ps_ajax.nonce, 'captcha': 'success', 'post_id': ps_ajax.post_id, 'type': 'captcha', 'protection': protection, 'captcha_id': ps_id, 'acf': acf, 'area': area, 'redirect': redirect },
                        success: function (response) {
                            if (true === response.success) {
                                // Redirect?
                                if (response.redirect) {
                                    window.location.replace(redirect);
                                }
                                // if no ajax.
                                if ('on' === ps_ajax.no_ajax) {
                                    Cookies.set('passster', btoa('captcha'), { expires: parseInt(ps_ajax.days) });
                                    window.location.reload();
                                } else {
                                    // set cookie if activated.
                                    if ('on' === ps_ajax.use_cookie) {
                                        Cookies.set('passster', btoa('captcha'), { expires: parseInt(ps_ajax.days) });
                                    }
                                    form.find('.passster-error').hide();
                                    // replace shortcodes.
                                    let content = response.content;

                                    $.each(ps_ajax.shortcodes, function (key, value) {
                                        content = content.replace(key, value);
                                    });

                                    $("#" + ps_id).replaceWith(content);
                                }
                            } else {
                                form.find('.passster-error').text(response.error);
                                form.find('.passster-error').show().fadeOut(2500);
                            }
                        }
                    });
                }

                if (response == 'error') {
                    form.find('.passster-error').text(ps_ajax.captcha_error);
                    form.find('.passster-error').show().fadeOut(2500);
                }
            }
        });

        document.querySelector('.captcha-form').addEventListener('submit', function (e) {
            e.preventDefault();
            captcha.validate()
        });
    }
});