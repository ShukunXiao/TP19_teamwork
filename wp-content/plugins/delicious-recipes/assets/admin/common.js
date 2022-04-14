function initTable() {
	if (
		jQuery(".dr-importRecipe").length > 0 &&
		!jQuery.fn.DataTable.isDataTable(".dr-importRecipe")
	) {
		return jQuery(".dr-importRecipe").DataTable({
			columnDefs: [{ orderable: false, targets: 0 }],
		});
	} else {
		return new jQuery.fn.dataTable.Api(".dr-importRecipe");
	}
}
/**
 * Scroll to div metabox.
 */
function delrecipe_tab_scrolltop(drUniqueClass) {
	let viewHolder = document.querySelector('.' + drUniqueClass + '-content');
	viewHolder.scrollIntoView(true);
	return false;
}
(function ($) {
	initTable();
})(jQuery);
jQuery(document).ready(function ($) {
	toastr.options.positionClass = "toast-top-full-width";
	toastr.options.timeOut = "5000";
	//toggle item js
	$(".dr-toggle-item:not(.active) .dr-toggle-content").hide();
	$(document).on("click", ".dr-toggle-title a", function () {
		$(this).parents(".dr-toggle-item").toggleClass("active");
		$(this)
			.parents(".dr-toggle-title")
			.siblings(".dr-toggle-content")
			.stop(true, false, true)
			.slideToggle();
	});

	//toggle onoff block popup
	$(document).on(
		"click",
		".dr-onoff-block:not(.dr-floated) .dr-onoff-toggle",
		function () {
			$(this).toggleClass("active");
			$(this)
				.siblings(".dr-onoff-popup")
				.stop(true, false, true)
				.slideToggle();
		}
	);

	//Email Templates Tabs
	$(document).on('click', '.dr-et-tab-btn', function () {
		var targetTab = $(this).data('target');
		$(this).addClass('active').siblings().removeClass('active');
		$(targetTab).fadeIn().siblings('.dr-email-templates-tab-item').hide();
		$('.dr-email-templates-tab-item').removeClass('show');
	})

	//toggle inlined onoff block popup
	$(document).on(
		"click",
		".dr-onoff-block.dr-floated .dr-onoff-toggle",
		function () {
			$(this).toggleClass("active");
			$(this).siblings(".dr-onoff-popup").fadeToggle();
		}
	);

	//main tab js
	$(document).on("click", ".dr-tab-wrap .dr-tab", function () {
		var drUniqueClass = $(this).attr("class").split(" ")[1];
		$(this).siblings(".dr-tab").removeClass("current");
		$(this).addClass("current");
		$(this)
			.parents(".dr-tab-wrap")
			.siblings(".dr-tab-content-wrap")
			.find(".dr-tab-content")
			.removeClass("current");
		$(this)
			.parents(".dr-tab-wrap")
			.siblings(".dr-tab-content-wrap")
			.find("." + drUniqueClass + "-content")
			.addClass("current");

		var DataTable = initTable();
		DataTable.destroy();
		initTable();
		delrecipe_tab_scrolltop(drUniqueClass);
	});

	//toggle disable class in repeater block
	$(document).on(
		"click",
		".dr-settings .dr-repeater-block .dr-system-btns .dr-toggle-btn",
		function () {
			$(this).parents(".dr-repeater-block").toggleClass("dr-disabled");
		}
	);

	// Featured Recipe
	$(document).on("click", ".dr-featured-recipe", function (e) {
		e.preventDefault();
		var featuredIcon = $(this);
		var post_id = $(this).attr("data-post-id");
		var nonce = $(this).attr("data-nonce");
		var data = {
			action: "featured_recipe",
			post_id: post_id,
			nonce: nonce,
		};
		$.ajax({
			url: ajaxurl,
			data: data,
			type: "post",
			dataType: "json",
			success: function (data) {
				if (data != "invalid") {
					featuredIcon
						.removeClass("dashicons-star-filled")
						.removeClass("dashicons-star-empty");
					if (data.new_status == "yes") {
						featuredIcon.addClass("dashicons-star-filled");
					} else {
						featuredIcon.addClass("dashicons-star-empty");
					}
				}
			},
		});
	});

	// MultiSelect support for Recipe categories widget
	if (jQuery.fn.select2) {
		$(".dr-recipe-categories-terms-holder select:visible").select2({
			allowClear: true,
			closeOnSelect: false,
			placeholder: "Select terms",
		});
	}

	//Category Lists
	$(document).on("change", ".dr-recipe-taxonomy-selector", function (e) {
		var selector = $(this);
		var taxonomy = selector.val();

		$.ajax({
			url: ajaxurl,
			data: {
				action: "dr_recipe_taxonomy_terms",
				taxonomy: taxonomy,
			},
			dataType: "json",
			context: this,
			type: "POST",
			success: function (response) {
				if(response.success){
					var responseData = Object.values(response.data);
					var item = {},
						items = [];
					responseData && responseData.forEach((element) => {
						item =
							'<option value="' +
							element.term_id +
							'">' +
							element.name +
							"</option>";
						items.push(item);
					});

					if (responseData.length == 0) {
						items = [
							"<option disabled selected>No Terms available.</option>",
						];
					}

					selector
						.parent()
						.siblings("p")
						.find(
							".dr-recipe-categories-terms-holder .dr-recipe-cat-select"
						)
						.html(items);

					if (jQuery.fn.select2) {
						$(this)
							.parent("p")
							.siblings("p")
							.find(".dr-recipe-categories-terms-holder select")
							.select2({
								allowClear: true,
								closeOnSelect: false,
								placeholder: "Select terms",
							});
					}
				}
			},
			error: function (errorThrown) {
				alert("Error...");
			},
		});
	});

	// Multiselect on widgets update
	// $(document).on("widget-updated", function (e, widget) {
	// 	console.log(widget, 'widget')
	// 	// "widget" represents jQuery object of the affected widget's DOM element
	// 	if (jQuery.fn.select2) {
	// 		$(
	// 			"#" +
	// 			widget[0].id +
	// 			" .dr-recipe-categories-terms-holder select"
	// 		).select2({
	// 			allowClear: true,
	// 			closeOnSelect: false,
	// 			placeholder: "Select terms",
	// 		});
	// 	}
	// });

	// $(document).on("widget-added", function (e, widget) {
	// 	console.log(widget, 'widget')
	// 	// "widget" represents jQuery object of the affected widget's DOM element
	// 	if (jQuery.fn.select2) {
	// 		$(
	// 			"#" +
	// 			widget[0].id +
	// 			" .dr-recipe-categories-terms-holder select"
	// 		).select2({
	// 			allowClear: true,
	// 			closeOnSelect: false,
	// 			placeholder: "Select terms",
	// 		});
	// 	}
	// });

	// Taxonomy SVG icon select
	$(document).on("click", ".dr-tab-icon-lists li", function () {
		var the = $(this),
			icon = the[0].classList[0],
			svg = the[0].innerHTML;

		if (icon == null || icon == "") {
			var data_prefix = the.children("svg").attr("data-prefix"),
				data_icon = the.children("svg").attr("data-icon"),
				icon = data_prefix + " fa-" + data_icon;
		}

		$(".dr-icon-holder").html(svg);
		$(".taxonomy_svg").val(icon);

		$(".dr-tab-icon-lists li").each(function () {
			$(this).removeClass("dr-selected-icon");
		});
		the.addClass("dr-selected-icon");
	});

	// Taxonomy FontAwesome icon search
	$(document).on("keyup", ".dr-adm-ico-search.fa-icon-search", function () {
		// Do simple filtering based on the search.
		var value = $(this).val();
		var matcher = new RegExp(value, "gi");
		$(this)
			.siblings(".dr-tab-icon-lists")
			.find("li")
			.show()
			.not(function () {
				return matcher.test($(this).find("svg").attr("data-icon"));
			})
			.hide();
	});

	$(document).on("keyup", ".dr-adm-ico-search.adm-png-search", function () {
		// Do simple filtering based on the search.
		var value = $(this).val();
		var matcher = new RegExp(value, "gi");
		$(this)
			.siblings(".dr-tab-icon-lists")
			.find("li")
			.show()
			.not(function () {
				return matcher.test($(this).attr("class"));
			})
			.hide();
	});

	$('input[type="text"].taxonomy_svg').each(function () {
		var $picker = $(".dr-recipe-icons-wrap");
		$picker.hide();
		// Show the picker when the input field gets focus.
		$(this).on("focusin", function () {
			$(".taxonomy_svg").addClass("dr-list-open");
			$picker.fadeIn(200);
		});

		$(this).on("change keyup", function () {
			var svg = $(this).val();
			if ("" === svg) {
				$(".dr-icon-holder").html("");
			}
		});

		var $search = $(".dr-adm-ico-search.adm-ico-search");
		// Do simple filtering based on the search.
		$search.on("keyup", function () {
			var search = $search.val().toLowerCase();
			var $icons = $(this).siblings(".dr-tab-icon-lists");
			$icons.find("li").css("display", "none");
			$icons
				.find("li:contains(" + search + ")")
				.css("display", "inline-block");
		});
	});

	// Hide icon picker when it or the input field loses focus.
	$(document).on("mouseup", function (e) {
		var $picker = $(".dr-recipe-icons-wrap");
		if (
			$picker.length &&
			!$picker.is(e.target) &&
			!$(e.target).hasClass("taxonomy_svg") &&
			$picker.has(e.target).length === 0
		) {
			$(".taxonomy_svg").removeClass("dr-list-open");
			$picker.fadeOut(200);
		}
	});

	// Clone recipe post when clone action is selected.
	$(document).on("click", ".dr-clone-recipe", function (e) {
		e.preventDefault();
		var post_id = $(this).data("post_id");
		var security = $(this).data("security");

		var data = {
			post_id: post_id,
			security: security,
			action: "dr_clone_recipe_data",
		};
		$.ajax({
			url: ajaxurl,
			data: data,
			type: "post",
			dataType: "json",
			success: function (data) {
				location.href = location.href;
			},
		});
	});

	$(document).on("submit", "form[name='dr-recipe-import']", function (e) {
		e.preventDefault();

		let importing_recipes = [];
		$("input[name='recipes[]").each(function () {
			if ($(this).is(":checked")) {
				importing_recipes.push(this.value);
			}
		});

		var data = $(this).serialize();
		var importer_uid = $(this)
			.find("input[name='dr_recipe_importer']")
			.val();
		var nonce = $(this).find("input[name='dr_import_recipes']").val();
		// console.log(data, importing_recipes, importer_uid);

		if (0 === importing_recipes.length) {
			toastr.error("Please select recipes to import first");
			return;
		}

		var data = {
			action: "dr_import_recipes",
			security: nonce,
			importer_uid: importer_uid,
			post_data: data,
			recipes: importing_recipes,
		};
		$.ajax({
			url: ajaxurl,
			data: data,
			type: "post",
			dataType: "json",
			beforeSend: function () {
				$("form[name='dr-recipe-import']")
					.find('input[type="submit"]')
					.attr("disabled", "disabled");
				$("form[name='dr-recipe-import']")
					.find('input[type="submit"]')
					.val("Importing...");
			},
			success: function (response) {
				var Table = initTable();
				debugger;
				response.data.recipes_imported &&
					response.data.recipes_imported.map(function (recipe_id) {
						Table.row(
							$("tr#dr_" + response.data.uid + "_" + recipe_id)
						)
							.remove()
							.draw();
					});
				toastr.success(
					"Successfully imported " +
					response.data.recipes_imported.length +
					" recipes"
				);
			},
			complete: function () {
				$("form[name='dr-recipe-import']")
					.find('input[type="submit"]')
					.removeAttr("disabled");
				$("form[name='dr-recipe-import']")
					.find('input[type="submit"]')
					.val("Import Selected Recipes / Taxonomies");
			},
		});
	});

	// Quick select functionality.
	$(document).on("click", "#dr-select-all", function (e) {
		// un(Check) all
		if ($("#dr-select-all").is(":checked")) {
			$(".dr-import-recipes")
				.find(":checkbox")
				.each(function () {
					$(this).prop("checked", true);
				});
		} else {
			$(".dr-import-recipes")
				.find(":checkbox")
				.each(function () {
					$(this).prop("checked", false);
				});
		}
	});
}); //document close
(function ($) {
	$(function () {
		// Add Color Picker to all inputs that have 'color-field' class
		$(".dr-colorpickr").wpColorPicker();
	});
	$(document).on("click", ".dr_tax_add_media_button", function (e) {
		e.preventDefault();
		var file_frame;
		var allowed_filetype = ["image/jpeg", "image/png"];
		if (file_frame) file_frame.close();

		file_frame = wp.media.frames.file_frame = wp.media({
			title: "Choose Taxonomy Image",
			button: {
				text: "Insert Image",
			},
			library: {
				type: allowed_filetype,
			},
			multiple: false,
		});

		file_frame.on("select", function () {
			var selection = file_frame.state().get("selection");
			selection.map(function (attachment, i) {
				var attachment = attachment.toJSON();
				$("#dr-tax-image-wrapper").html(
					'<img src="' + attachment.sizes.thumbnail.url + '"/>'
				);
				$(".dr_tax_image_media_id").val(attachment.id);
			});
		});

		file_frame.open();
	});

	$(document).on("click", ".dr_tax_remove_media_remove", function (e) {
		e.preventDefault();
		$("#dr-tax-image-wrapper").html("");
		$(".dr_tax_image_media_id").val("");
	});

	// Icon Picker
	$(function () {
		var activeIndex = $(".active-tab").index(),
			$contentlis = $(".dr-tabs-content .dr-tab-content-inn"),
			$tabslis = $(".dr-tab-titles li");

		// Show content of active tab on loads
		$contentlis.eq(activeIndex).show();

		$(".dr-tab-titles").on("click", "li", function (e) {
			var $current = $(e.currentTarget),
				index = $current.index();

			$tabslis.removeClass("active-tab");
			$current.addClass("active-tab");
			$contentlis.hide().eq(index).show();
		});
	});
})(jQuery);
