/**
 * Handles the options in the surprise me nav menu metabox.
 *
 * @package Polylang
 */

jQuery(document).ready(function ($) {
	$("#update-nav-menu").on("click", function (e) {
		if (
			e.target &&
			e.target.className &&
			-1 != e.target.className.indexOf("item-edit")
		) {
			$("input[value='#dr_surprise_me'][type=text]")
				.parent()
				.parent()
				.parent()
				.each(function () {
					var item = $(this).attr("id").substring(19);
					$(this).children("p:not( .field-move )").remove(); // remove default fields we don't need

					var menu_title = $("#menu-item-" + item)
						.find("span.menu-item-title")
						.html();

					// debugger;

					// item is a number part of id of parent menu item built by WordPress
					// delicious_recipes_data is built server side with i18n strings without HTML and data retrieved from post meta
					// the usage of attr method is safe before append call.
					h = $("<input>").attr({
						type: "hidden",
						id: "edit-menu-item-title-" + item,
						name: "menu-item-title[" + item + "]",
						value: menu_title,
					});
					$(this).append(h); // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.append

					h = $("<input>").attr({
						type: "hidden",
						id: "edit-menu-item-url-" + item,
						name: "menu-item-url[" + item + "]",
						value: "#dr_surprise_me",
					});
					$(this).append(h); // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.append

					// a hidden field which exits only if our jQuery code has been executed
					h = $("<input>").attr({
						type: "hidden",
						id: "edit-menu-item-dr-detect-" + item,
						name: "menu-item-dr-detect[" + item + "]",
						value: 1,
					});
					$(this).append(h); // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.append

					ids = Array(
						"show_posts",
						"show_text_icon",
						"show_text",
						"show_icon"
					); // reverse order

					// add the fields
					for (
						var i = 0, idsLength = ids.length;
						i < idsLength;
						i++
					) {
						p = $("<p>").attr("class", "description");
						// p is hardcoded just above by using attr method which is safe.
						$(this).prepend(p); // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.prepend
						// item is a number part of id of parent menu item built by WordPress
						// delicious_recipes_data is built server side with i18n strings without HTML
						label = $("<label>")
							.attr(
								"for",
								"edit-menu-item-" + ids[i] + "-" + item
							)
							.text(" " + delicious_recipes_data.strings[ids[i]]);
						p.append(label); // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.append
						cb = $("<input>").attr({
							type: "checkbox",
							id: "edit-menu-item-" + ids[i] + "-" + item,
							name: "menu-item-" + ids[i] + "[" + item + "]",
							value: 1,
						});
						if (
							(typeof delicious_recipes_data.val[item] !=
								"undefined" &&
								delicious_recipes_data.val[item][ids[i]] ==
									1) ||
							(typeof delicious_recipes_data.val[item] ==
								"undefined" &&
								ids[i] == "show_text_icon")
						) {
							// show_names as default value
							cb.prop("checked", true);
						}
						// See reasons above. Checkbox are totaly hardcoded here with safe value
						label.prepend(cb); // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.prepend
					}
				});

			// disallow unchecking both show icons and show text
			$(".menu-item-data-object-id").each(function () {
				var id = $(this).val();
				var options = ["icon-", "text-"];
				$.each(options, function (i, v) {
					$("#edit-menu-item-show_" + v + id).on ( "change", function () {
						if (true != $(this).prop("checked")) {
							$(
								"#edit-menu-item-show_" + options[1 - i] + id
							).prop("checked", true);
						}
					});
				});
			});
		}
	});
});
