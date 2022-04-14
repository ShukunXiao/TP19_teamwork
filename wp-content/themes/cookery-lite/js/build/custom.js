jQuery(document).ready(function($) {

    var slider_auto, slider_loop, rtl, header_layout, winWidth;

    if (cookery_lite_data.auto == '1') {
        slider_auto = true;
    } else {
        slider_auto = false;
    }

    if (cookery_lite_data.loop == '1') {
        slider_loop = true;
    } else {
        slider_loop = false;
    }

    if (cookery_lite_data.rtl == '1') {
        rtl = true;
    } else {
        rtl = false;
    }

    //header search form toggle 
    $('.header-search .search-toggle').click(function() {
        $(this).siblings('.header-search-wrap').fadeIn();
        // $('.header-search-wrap form .search-field').focus();
    });

    $('.header-search .close').click(function() {
        $(this).parents('.header-search-wrap').fadeOut();
    });

    $(window).keyup(function(e) {
        if (e.key == 'Escape') {
            $('.header-search .header-search-wrap').fadeOut();
        }
    });

    //add submenu toggle btn
    $('.nav-menu li.menu-item-has-children').find('> a').after('<button class="submenu-toggle"><i class="fas fa-chevron-down"></i></button>');

    //add tabindex for submenu toggle button
    // $('.site-header:not(.style-four) .nav-menu li button').attr('tabindex', -1);

    $('.menu-item-has-children .submenu-toggle').on('click', function() {
        $(this).toggleClass('active');
        $(this).siblings('.sub-menu').stop(true, false, true).slideToggle();
    });

    //toggle main navigation
    $('.main-navigation .toggle-btn').click(function() {
        $(this).parents('.main-navigation').addClass('menu-toggled');
        $(this).siblings('.main-menu-modal').animate({
            width: 'toggle',
        });
    });

    $('.main-navigation .close').click(function() {
        $(this).parents('.main-navigation').removeClass('menu-toggled');
        $(this).parents('.main-menu-modal').animate({
            width: 'toggle',
        });
    });

    $(window).keyup(function(event) {
        if (event.key == 'Escape') {
            $('.main-navigation').removeClass('menu-toggled');
        }
    });

    //mobile menu toggle
    $('.mobile-header .toggle-btn').click(function() {
        $(this).parents('.mobile-header').addClass('menu-active');
        $('.mobile-header-popup').fadeIn();
    });

    $('.mobile-header .primary-menu-list .close').click(function() {
        $(this).parents('.mobile-header').removeClass('menu-active');
        $('.mobile-header-popup').fadeOut();
    });

    $(window).keyup(function(event) {
        if (event.key == 'Escape') {
            $(this).parents('.mobile-header').removeClass('menu-active');
            $('.mobile-header-popup').fadeOut();
        }
    });

    //mobile menu top space
    $(window).on('resize load scroll', function() {
        var adminBarHeight = $('#wpadminbar').outerHeight();
        var mblHeaderHeight = $('.mobile-header').outerHeight();
        var mblAddedHeight3 = parseInt(mblHeaderHeight) + parseInt(adminBarHeight);

        if ($('.admin-bar').length) {
            $('.mobile-header .mobile-header-popup').css('top', mblAddedHeight3);

        } else {
            $('.mobile-header .mobile-header-popup').css('top', mblHeaderHeight);
        }

        $(window).scroll(function() {
            if ($('.mobile-header').hasClass('stick')) {
                $('.mobile-header .mbl-menu-wrap').css('top', mblHeaderHeight);
            }
        });

        //sticky floated meta        
        if ($('#wpadminbar').length) {
            $('.single .site-main article .floated-meta-inner').css('top', adminBarHeight + 20);
        } else {
            $('.single .site-main article .floated-meta-inner').css('top', 20);
        }
    });

    //move site branding inside main navigation
    $('.hide-element').removeClass('hide-element');

    $(window).on('resize load', function() {

        //push social share from bottom
        $('.site-main article .entry-content').each(function() {
            var articalContentH = $(this).outerHeight();
            $(this).siblings('.entry-header').children('.floated-meta').css('bottom', articalContentH);
        });

    });

    //move floated meta into entry content for recipe one
    $('.single-recipe-one .site-main article .floated-meta').prependTo('.single-recipe-one .site-main article .dr-entry-content');

    //back to top
    $(window).scroll(function() {
        if ($(this).scrollTop() > 200) {
            $('.back-to-top').addClass('active');
        } else {
            $('.back-to-top').removeClass('active');
        }
    });

    $('.back-to-top').on('click', function() {
        $('body,html').animate({
            scrollTop: 0,
        }, 600);
    });

    //for accessibility
    $('.nav-menu li a, .nav-menu ul li button').focus(function() {
        $(this).parents('li').addClass('hover');
    }).blur(function() {
        $(this).parents('li').removeClass('hover');
    });

    //slider one
    if ($('.banner-slider.style-one .item-wrapper .item').length > 4) {
        sliderLoop1 = true;
    } else {
        sliderLoop1 = false;
    }
    $('.banner-slider.style-one .item-wrapper').owlCarousel({
        items: 4,
        autoplay: slider_auto,
        loop: sliderLoop1,
        nav: true,
        dots: false,
        rewind: false,
        autoplaySpeed: 800,
        autoplayTimeout: cookery_lite_data.speed,
        rtl: rtl,
        navText: [
            '<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689"><g transform="translate(17.729 11.628) rotate(180)"><path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/><path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/></g></svg>',
            '<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689"><g transform="translate(0.75 1.061)"><path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/><path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/></g></svg>'
        ],
        responsive: {

            0: {
                items: 1,
            },
            768: {
                items: 2,
            },
            1025: {
                items: 3,
            },
            1200: {
                items: 4,
            }
        }
    });

    //change newsletter input submit into button submit
    $('.blossomthemes-email-newsletter-wrapper form input[type="submit"]').each(function() {

        var submitClass = $(this).attr('class');
        var submitName = $(this).attr('name');
        var submitValue = $(this).attr('value');

        $(this).replaceWith('<button type="submit" class="' + submitClass + '" name="' + submitName + '">' + submitValue + '</button>');

    });

    //promo section slider
    $('.promo-section .bttk-itw-holder').addClass('owl-carousel');
    if ($('.promo-section .bttk-itw-holder li').length > 3) {
        itemLoop1 = true;
    } else {
        itemLoop1 = false;
    }
    $('.promo-section .widget_bttk_image_text_widget .bttk-itw-holder').owlCarousel({
        items: 3,
        autoplay: false,
        loop: itemLoop1,
        nav: true,
        dots: false,
        margin: 60,
        autoplaySpeed: 800,
        autoplayTimeout: 3000,
        navText: [
            '<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689"><g transform="translate(17.729 11.628) rotate(180)"><path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/><path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/></g></svg>',
            '<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689"><g transform="translate(0.75 1.061)"><path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/><path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/></g></svg>'
        ],
        responsive: {
            0: {
                items: 1,
                margin: 30,
            },
            768: {
                items: 2,
                margin: 30,
            },
            1025: {
                items: 3,
                margin: 30,
            },
            1200: {
                margin: 60,
            }
        }
    });

    //featured recipe section
    if ($('.featured-recipe-section .section-grid .section-block').length > 5) {
        itemLoop2 = true;
    } else {
        itemLoop2 = false;
    }
    $('.featured-recipe-section.style-one .section-grid').owlCarousel({
        items: 5,
        autoplay: true,
        loop: itemLoop2,
        nav: true,
        dots: false,
        autoplaySpeed: 800,
        autoplayTimeout: 3000,
        navText: [
            '<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689"><g transform="translate(17.729 11.628) rotate(180)"><path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/><path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/></g></svg>',
            '<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689"><g transform="translate(0.75 1.061)"><path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/><path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/></g></svg>'
        ],
        responsive: {

            0: {
                items: 1,
            },
            768: {
                items: 2,
            },
            1025: {
                items: 3,
            },
            1200: {
                items: 4,
            },
            1366: {
                items: 5,
            }
        }
    });

    //pull recipe category title left
    $('.dr-category a').each(function() {
        var recipeCatWidth = $(this).width();
        var recipeCatTitleWidth = $(this).children('.cat-name').outerWidth();
        var catPullValue = (parseInt(recipeCatTitleWidth) - parseInt(recipeCatWidth)) / 2;
        if ($('body').hasClass('rtl')) {
            $(this).children('.cat-name').css({
                'left': 'auto',
                'right': -catPullValue,
            });
        } else {
            $(this).children('.cat-name').css('left', -catPullValue);
        }
    });

    //wrap client section widget title text
    $('.client-section .widget .widget-title, .elem-client-section .elementor-widget-heading .elementor-heading-title').wrapInner('<span></span>');

    //js for masonry grid
    $('.error404 .additional-post .section-grid').imagesLoaded(function() {

        $('.error404 .additional-post .section-grid').masonry({
            itemSelector: 'article',
        });

    });

    //Gutenberg alignfull js
    $(window).on('load resize', function() {
        var gbWindowWidth = $(window).width();
        var gbContainerWidth = $('.cookery-has-blocks .site-content > .container').width();
        var gbContentWidth = $('.cookery-has-blocks .site-main .entry-content').width();
        var gbMarginFull = (parseInt(gbContainerWidth) - parseInt(gbWindowWidth)) / 2;
        var gbMarginCenter = (parseInt(gbContentWidth) - parseInt(gbWindowWidth)) / 2;

        $(".cookery-has-blocks.full-width .site-main .entry-content .alignfull").css({ "max-width": gbWindowWidth, "width": gbWindowWidth, "margin-left": gbMarginFull });

        $(".cookery-has-blocks.full-width.centered .site-main .entry-content .alignfull").css({ "max-width": gbWindowWidth, "width": gbWindowWidth, "margin-left": gbMarginCenter });

    });

    /** Lightbox */
    if (cookery_lite_data.lightbox == '1') {
        $('.entry-content').find('.gallery-columns-1').find('.gallery-icon > a').attr('data-fancybox', 'group1');
        $('.entry-content').find('.gallery-columns-2').find('.gallery-icon > a').attr('data-fancybox', 'group2');
        $('.entry-content').find('.gallery-columns-3').find('.gallery-icon > a').attr('data-fancybox', 'group3');
        $('.entry-content').find('.gallery-columns-4').find('.gallery-icon > a').attr('data-fancybox', 'group4');
        $('.entry-content').find('.gallery-columns-5').find('.gallery-icon > a').attr('data-fancybox', 'group5');
        $('.entry-content').find('.gallery-columns-6').find('.gallery-icon > a').attr('data-fancybox', 'group6');
        $('.entry-content').find('.gallery-columns-7').find('.gallery-icon > a').attr('data-fancybox', 'group7');
        $('.entry-content').find('.gallery-columns-8').find('.gallery-icon > a').attr('data-fancybox', 'group8');
        $('.entry-content').find('.gallery-columns-9').find('.gallery-icon > a').attr('data-fancybox', 'group9');
        $('.video-section .video-play-icon').attr('data-fancybox', 'group10');

        $("a[href$='.jpg'],a[href$='.jpeg'],a[href$='.png'],a[href$='.gif'],[data-fancybox],.video-section .video-play-icon").fancybox({
            buttons: [
                "zoom",
                //"share",
                "slideShow",
                "fullScreen",
                //"download",
                //"thumbs",
                "close"
            ]
        });
    }

    /**
     * First Letter of word to Drop Cap
     * https://stackoverflow.com/questions/5458605/first-word-selector 
     * https://paulund.co.uk/capitalize-first-letter-string-javascript
     */
    $.fn.wrapStart = function(numWords) {
        var node = this.contents().filter(function() {
                return this.nodeType == 3;
            }).first(),
            text = node.text(),
            first = text.split(" ", numWords).join(" ");
        firstLetter = first.charAt(0);
        finale = '<span class="dropcap">' + firstLetter + '</span>' + first.slice(1);

        if (!node.length)
            return;

        node[0].nodeValue = text.slice(first.length);
        node.before(finale);
    };
    if (($('.post-template-default').length > 0 || ($('.page-template-default').length > 0 && $('.home').length == 0 && !($("body").hasClass("elementor-editor-active")))) && cookery_lite_data.drop_cap == 1) {
        $('.entry-content p').wrapStart(1);
    }

    //Ajax for Add to Cart
    $('.btn-simple').click(function() {
        $(this).addClass('adding-cart');
        var product_id = $(this).attr('id');

        $.ajax({
            url: cookery_lite_data.ajax_url,
            type: 'POST',
            data: 'action=cookery_lite_add_cart_single&product_id=' + product_id,
            success: function(results) {
                $('#' + product_id).replaceWith(results);
            }
        }).done(function() {
            var cart = $('#cart-' + product_id).val();
            $('.cart .number').html(cart);
        });
    });

});