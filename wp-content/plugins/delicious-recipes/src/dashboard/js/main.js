(function ($) {

    if (document.querySelector('.dr-ud__sidebar')) {
        var drUdSidebar = document.querySelector('.dr-ud__sidebar');
        var sidebarToggleBtn = drUdSidebar.querySelector('.dr-sidebar-toggle-btn');
        if (drUdSidebar && sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', function () {
                drUdSidebar.classList.toggle('collapsed');
                var dr_archive_list_wrapper = document.querySelector('.dr-archive-list-wrapper');
                if (dr_archive_list_wrapper) {
                    dr_archive_list_wrapper.classList.toggle('collapsed-sidebar');
                }
            })
        }
    }

    if (document.querySelector('.view-layout-buttons')) {
        var grid_view_btn = document.getElementById('grid-view');
        var list_view_btn = document.getElementById('list-view');
        var dr_archive_list = document.querySelector('.dr-archive-list');
        grid_view_btn.addEventListener('click', function () {
            dr_archive_list.classList.add('transitioning')

            this.classList.add('active');
            list_view_btn.classList.remove('active');
            setTimeout(function () {
                dr_archive_list.classList.remove('transitioning')
                dr_archive_list.classList.remove('list-view');
                dr_archive_list.classList.add('grid-view');
            }, 300)
        })
        list_view_btn.addEventListener('click', function () {
            dr_archive_list.classList.add('transitioning')
            this.classList.add('active');
            grid_view_btn.classList.remove('active');
            setTimeout(function () {
                dr_archive_list.classList.remove('transitioning')
                dr_archive_list.classList.remove('grid-view');
                dr_archive_list.classList.add('list-view');
            }, 300)
        })
    }

    // scrollspy
    // $(window).bind('scroll', function () {
    //     var currentTop = $(window).scrollTop();
    //     var elems = $('.dr-ud__main-inner > div');

    //     elems.each(function (index) {
    //         var elemTop = $(this).offset().top;
    //         var elemBottom = elemTop + $(this).height();
    //         if (currentTop >= elemTop && currentTop <= elemBottom) {
    //             var id = $(this).attr('id');
    //             var navElem = $('.user-menu a[data-target="' + id + '"]');
    //             navElem.parent().addClass('active').siblings().removeClass('active');
    //         }
    //     })
    // })

    //main tab js
    $(document).on("click", ".dr-ud__sidebar .dr-ud__user-menu .dr-ud_tab", function () {
        var drUniqueClass = $(this).attr("class").split(" ")[1];
        $(this).siblings(".dr-ud_tab").removeClass("active");
        $(this).addClass("active");
        $(this)
            .parents(".dr-ud__sidebar")
            .siblings(".dr-ud__main-wrap")
            .find(".dr-ud__main-inner")
            .removeClass("active");
        $(this)
            .parents(".dr-ud__sidebar")
            .siblings(".dr-ud__main-wrap")
            .find(".dr-ud-" + drUniqueClass + "-content")
            .addClass("active");

        deluserdb_tab_scrolltop(drUniqueClass);
    });

    // pw show toggle btn
    $(document).on('click', '.dr-input-wrap.has-pw-toggle-btn .pw-toggle-btn', function (e) {

        e.preventDefault();
        let element = $(this);
        let inputSibling = element.siblings('.dr-form__field-input.password');
        inputSibling[0].type = inputSibling[0].type == 'text' ? 'password' : 'text';
        let inputParent = element.parent();
        inputParent[0].classList.toggle('pw-show');

    });

    if (document.querySelector('#profile-img')) {
        var thisDZContainer_profile_img = $("#profile-img");

        var DZOBJ_profile_img = new Dropzone("#profile-img", {
            acceptedFiles: "image/jpeg, image/gif, image/png",
            maxFiles: 1,
            url: delicious_recipes.ajax_url,
            uploadMultiple: false,
            resizeWidth: 1500,
            resizeMimeType: 'image/jpeg',
            resizeMethod: 'crop',
            resizeQuality: 65,
            createImageThumbnails: false,
            maxFilesize: 2,
            dictDefaultMessage: delicious_recipes.edit_profile_pic_msg
        });

        DZOBJ_profile_img.on("sending", function (file, xhr, formData) {
            var nonce = document.getElementsByName("profile_image_nonce")[0].value;
            formData.append("action", "delicious_recipes_profile_image_upload");
            formData.append("nonce", nonce);
        });

        DZOBJ_profile_img.on("success", function (file, response) {
            if (response !== "error") {
                var file_data = JSON.parse(response);
                thisDZContainer_profile_img.find(".img").html("<img src='" + file_data.url + "'>");
                thisDZContainer_profile_img.find("input[name='profile_image']").val(file_data.file);
                thisDZContainer_profile_img.find("input[name='profile_image_url']").val(file_data.url);

            }
        });

        DZOBJ_profile_img.on("addedfile", function (file) {
            thisDZContainer_profile_img.find(".dr-profile-img-delete").css("display", "block");
            thisDZContainer_profile_img.find(".dr-profile-img-delete").on("click", function (e) {
                thisDZContainer_profile_img.find("input[name='profile_image']").val('');
                thisDZContainer_profile_img.find("input[name='profile_image_url']").val('');
                DZOBJ_profile_img.removeFile(file);
                thisDZContainer_profile_img.find(".dr-profile-img-delete").css("display", "none");
            });
        });
    }

    $(document).on('click', '.dr-profile-btns .dr-profile-img-delete', function (e) {

        DZOBJ_profile_img.removeAllFiles();
        thisDZContainer_profile_img.find("input[name='profile_image']").val('');
        thisDZContainer_profile_img.find("input[name='profile_image_url']").val('');
        thisDZContainer_profile_img.find(".img img").remove();
        thisDZContainer_profile_img.find(".dr-profile-img-delete").css("display", "none");

    });

    $("form[name='dr-form__sign-up']").parsley();

})(jQuery)

/**
 * Scroll to div metabox.
 */
function deluserdb_tab_scrolltop(drUniqueClass) {
    let viewHolder = document.querySelector('.dr-ud-' + drUniqueClass + '-content');
    viewHolder.scrollIntoView(true);
    return false;
}