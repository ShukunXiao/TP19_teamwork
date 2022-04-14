(function () {
	class recipeGlobal {
		constructor() {}
		static initLightGallery(content) {
			var _videos = content.querySelectorAll(".dr-instruction-videopop");
			if (_videos) {
				_videos.forEach(function (_video) {
					lightGallery(_video, { selector: "this" });
				});
			}
		}

		static initRatings(content) {
			jQuery(content)
				.find(".dr-comment-form-rating")
				.each(function () {
					jQuery(this).rateYo({
						halfStar: true,
						onChange: function (rating, rateYoInstance) {
							// jQuery('.comment-rating-field-message').hide();
							jQuery(this)
								.parent()
								.find('input[name="rating"]')
								.val(rating);
						},
					});
				});
		}
	}

	window["recipe_global"] = recipeGlobal;
})();

(function ($) {
	var rtl;
	if ($("body").hasClass("rtl")) {
		rtl = true;
	} else {
		rtl = false;
	}
	$(".dr-post-carousel").owlCarousel({
		items: 3,
		autoplay: false,
		loop: false,
		nav: true,
		dots: false,
		rewind: true,
		margin: 30,
		autoplaySpeed: 800,
		autoplayTimeout: 3000,
		rtl: rtl,
		navText: [
			'<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689"><g transform="translate(17.729 11.628) rotate(180)"><path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/><path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/></g></svg>',
			'<svg xmlns="http://www.w3.org/2000/svg" width="18.479" height="12.689" viewBox="0 0 18.479 12.689"><g transform="translate(0.75 1.061)"><path d="M7820.11-1126.021l5.284,5.284-5.284,5.284" transform="translate(-7808.726 1126.021)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/><path d="M6558.865-354.415H6542.66" transform="translate(-6542.66 359.699)" fill="none" stroke="#232323" stroke-linecap="round" stroke-width="1.5"/></g></svg>',
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
		},
	});

	// Search Filters
	$(".js-select2").select2({
		closeOnSelect: false,
		placeholder: delicious_recipes.search_placeholder,
		allowClear: true,
	});

	$("body").on("change", ".dr-search-field select", function () {
		var choices = {};
		$(".dr-search-field select option").each(function () {
			if ($(this).is(":selected")) {
				if (!choices.hasOwnProperty($(this).attr("name"))) {
					choices[$(this).attr("name")] = [];
				}
				var idx = $.inArray(this.value, choices[$(this).attr("name")]);
				if (idx == -1) {
					choices[$(this).attr("name")].push(this.value);
				}
			}
		});

		nonce = $("#dr-search-nonce").val();

		jQuery.ajax({
			url: delicious_recipes.ajax_url,
			data: {
				action: "recipe_search_results",
				search: choices,
				nonce: nonce,
			},
			dataType: "json",
			type: "post",
			beforeSend: function () {
				$(".dr-search-item-wrap").addClass("dr-loading");
			},
			success: function (response) {
				if (response.success) {
					var template = wp.template("search-block-tmp");
					$(".dr-search-item-wrap").html(
						template(response.data.results)
					);
					$(".navigation.pagination .nav-links")
						.addClass("dr-ajax-paginate")
						.html(response.data.pagination);
				}
			},
			complete: function () {
				$(".dr-search-item-wrap").removeClass("dr-loading");
			},
		});
	});

	$(document).on("click", ".dr-ajax-paginate a.page-numbers", function (e) {
		e.preventDefault();
		var choices = {};
		$(".dr-search-field select option").each(function () {
			if ($(this).is(":selected")) {
				if (!choices.hasOwnProperty($(this).attr("name"))) {
					choices[$(this).attr("name")] = [];
				}
				var idx = $.inArray(this.value, choices[$(this).attr("name")]);
				if (idx == -1) {
					choices[$(this).attr("name")].push(this.value);
				}
			}
		});

		nonce = $("#dr-search-nonce").val();

		jQuery.ajax({
			url: delicious_recipes.ajax_url,
			data: {
				action: "recipe_search_results",
				search: choices,
				nonce: nonce,
				paged: $(this).attr("href").split("=")[1],
			},
			dataType: "json",
			type: "post",
			beforeSend: function () {
				$(".dr-search-item-wrap").addClass("dr-loading");
			},
			success: function (response) {
				if (response.success) {
					var template = wp.template("search-block-tmp");
					$(".dr-search-item-wrap").html(
						template(response.data.results)
					);
					$(".navigation.pagination .nav-links")
						.addClass("dr-ajax-paginate")
						.html(response.data.pagination);
				}
			},
			complete: function () {
				$(".dr-search-item-wrap").removeClass("dr-loading");
			},
		});
	});

	//show/hide social share
	$(".post-share a.meta-title").on("click", function (e) {
		e.stopPropagation();
		$(this).siblings(".social-networks").slideToggle();
	});

	$(".post-share").on("click", function (e) {
		e.stopPropagation();
	});

	$("body, html").on("click", function () {
		$(".post-share .social-networks").slideUp();
	});

	//pull recipe category title left
	$(".dr-category a, .post-navigation article .dr-category > span").each(
		function () {
			var recipeCatWidth = $(this).width();
			var recipeCatTitleWidth = $(this)
				.children(".cat-name")
				.outerWidth();
			var catPullValue =
				(parseInt(recipeCatTitleWidth) - parseInt(recipeCatWidth)) / 2;
			$(this).children(".cat-name").css("left", -catPullValue);
			if ($("body").hasClass("rtl")) {
				$(this).children(".cat-name").css({
					left: "auto",
					right: -catPullValue,
				});
			} else {
				$(this).children(".cat-name").css("left", -catPullValue);
			}
		}
	);

	/** Ajax call for recipe like */
	$(document).on("click", ".dr_like__recipe", function (e) {
		e.preventDefault();
		var container = $(this);
		id = container.attr("id").split("-").pop();

		if (container.hasClass("recipe-liked")) {
			container.removeClass("recipe-liked");
			container.addClass("like-recipe");
			var addRemove = "remove";
		} else {
			container.removeClass("like-recipe");
			container.addClass("recipe-liked");
			var addRemove = "add";
		}

		$.ajax({
			type: "post",
			url: delicious_recipes.ajax_url,
			data: { action: "recipe_likes", add_remove: addRemove, id: id },
			beforeSend: function () {
				container.addClass("loading");
			},
			success: function (data) {
				container.attr("title", data.data.likes);
				container.find(".dr-likes-total").html(data.data.likes_count);
			},
		}).done(function () {
			container.removeClass("loading");
		});
	});

	/****   Wishlist a Recipe   ****/
	if ($(".dr-recipe-wishlist span.dr-bookmark-wishlist").length) {
		$(document).on(
			"click",
			".dr-recipe-wishlist span.dr-bookmark-wishlist",
			function (e) {
				e.preventDefault();
				var thisHeart = $(this),
					recipeID = thisHeart.data("recipe-id");

				if (thisHeart.hasClass("dr-wishlist-is-bookmarked")) {
					thisHeart.removeClass("dr-wishlist-is-bookmarked");
					var addRemove = "remove";
				} else {
					thisHeart.addClass("dr-wishlist-is-bookmarked");
					var addRemove = "add";
				}

				$.ajax({
					type: "post",
					url: delicious_recipes.ajax_url,
					data: {
						action: "delicious_recipes_wishlist",
						add_remove: addRemove,
						recipe_id: recipeID,
					},
					beforeSend: function () {
						thisHeart.addClass("loading");
					},
					success: function (data) {
						thisHeart
							.find(".dr-wishlist-total")
							.html(data.data.wishlists);
						thisHeart
							.find(".dr-wishlist-info")
							.html(data.data.message);
					},
				}).done(function () {
					thisHeart.removeClass("loading");
				});
			}
		);
	}
	if ($(".dr-recipe-wishlist span.dr-popup-user__registration").length) {
		// Get the modal
		var modal = document.getElementById(
			"dr-user__registration-login-popup"
		);

		// Get the button that opens the modal
		var _popupBtns = content.querySelectorAll(
			".dr-popup-user__registration"
		);
		if (_popupBtns) {
			_popupBtns.forEach(function (_popupBtn) {
				_popupBtn.addEventListener("click", function (e) {
					e.preventDefault();
					modal.style.display = "block";
				});
			});
		}

		// Get the <span> element that closes the modal
		var span = document.getElementsByClassName(
			"dr-user__registration-login-popup-close"
		)[0];

		// When the user clicks on <span> (x), close the modal
		span.onclick = function () {
			modal.style.display = "none";
		};

		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function (event) {
			if (event.target == modal) {
				modal.style.display = "none";
			}
		};

		$(document).on("submit", "form[name='dr-form__log-in']", function (e) {
			e.preventDefault();

			var loginform = $(this);
			var username = loginform.find('input[name="username"]').val();
			var password = loginform.find('input[name="password"]').val();
			var rememberme = loginform.find('input[name="rememberme"]').val();
			var login = loginform.find('input[name="login"]').val();
			var nonce = loginform
				.find('input[name="delicious_recipes_user_login_nonce"]')
				.val();

			$.ajax({
				url: delicious_recipes.ajax_url,
				data: {
					action: "delicious_recipes_process_login",
					username: username,
					password: password,
					rememberme: rememberme,
					login: login,
					delicious_recipes_user_login_nonce: nonce,
					calling_action: "delicious_recipes_modal_login",
				},
				dataType: "json",
				type: "post",
				beforeSend: function () {
					loginform.addClass("dr-loading");
					$(".delicious-recipes-success-msg")
						.hide();
					$(".delicious-recipes-error-msg")
						.hide();		
				},
				success: function (response) {
					if (response.success) {
						$(".dr-recipe-wishlist > span").removeClass(
							"dr-popup-user__registration"
						);
						$(".dr-recipe-wishlist > span").addClass(
							"dr-bookmark-wishlist"
						);
						$(".delicious-recipes-success-msg")
							.html(response.data.success)
							.show();
						location.reload();
					} else {
						console.log(response.data.error);
						$(".delicious-recipes-error-msg")
							.html(response.data.error)
							.show();
					}
				},
				complete: function () {
					loginform.removeClass("dr-loading");
				},
			});
		});
	}

	$("#dr-recipes-clear-filters").on("click", function (e) {
		e.preventDefault();
		$(".dr-advance-search .advance-search-options select").each(
			function () {
				$(this).val(null).trigger("change");
			}
		);
	});

	// recipe instruction
	$('.dr-inst-mark-read input[type="checkbox"]').each(function () {
		$(this).on("click", function () {
			if ($(this).prop("checked") == true) {
				$(this).parents("li").addClass("dr-instruction-checked");
			} else {
				$(this).parents("li").removeClass("dr-instruction-checked");
			}
		});
	});

	$("form[name='dr-form__log-in']").parsley();

	$(document).on("click", ".dr-ud_tab", function () {
		$(".dr-ud__sidebar").removeClass("collapsed");
	});
})(jQuery);

window.addEventListener("load", recipeScripts());

function recipeScripts() {
	// creating utils
	var Util = function () {};
	Util.on = function (eventName, selector, callback) {
		document.addEventListener(
			eventName,
			function (event) {
				for (
					var elTarget = event.target;
					elTarget && elTarget != this;
					elTarget = elTarget.parentNode
				) {
					if (elTarget.matches(selector)) {
						callback.call(elTarget, event);
						break;
					}
				}
			},
			false
		);
	};

	window["delicious_recipes"]["utilities"] = Util;

	// gallary modal
	Util.on("click", ".view-gallery-btn", function (event) {
		var targetEl = this;
		var images = JSON.parse(targetEl.dataset.lgSettings);
		var allimages = images.map(function (img) {
			return { src: img.previewURL };
		});
		var viewGallery = window.lightGallery(event.target, {
			dynamic: true,
			dynamicEl: allimages,
		});
		viewGallery.openGallery(0);
	});

	// video hide toggle
	Util.on("click", ".dr-video-toggle", function (e) {
		if (e.target.matches(".dr-video-toggle")) {
			let videoTargets = document.querySelectorAll(
				e.target.getAttribute("data-target")
			);
			videoTargets.forEach(function (video) {
				if (video.style.display === "none") {
					video.style.display = "block";
				} else {
					video.style.display = "none";
				}
			});
		}
	});

	// ingredients
	["keyup", "mouseup"].forEach(function (evt) {
		document.addEventListener(evt, function (e) {
			if (e.target.classList.contains("dr-scale-ingredients")) {
				let ingredient = e.target.closest(".dr-ingredients-list");
				let newServe = e.target.value;
				let recipeID = e.target.getAttribute("data-recipe");
				newServe = Number(newServe);
				let originalServe = e.target.getAttribute("data-original");
				originalServe = Number(originalServe);
				let ingredientQuantities = ingredient.querySelectorAll(
					".ingredient_quantity"
				);
				let printBtn = document.getElementById(
					"dr-single-recipe-print-" + recipeID
				);
				let default_print_lnk = printBtn.getAttribute("href");
				var default_print_attrs = default_print_lnk.split("?");
				// This may need something more complex...
				var new_print_attrs =
					"print_recipe=true&recipe_servings=" + newServe;
				// This changes the href of the link to the new one.
				printBtn.setAttribute(
					"href",
					default_print_attrs[0] + "?" + new_print_attrs
				);
				ingredientQuantities.forEach(function (qty) {
					let ingredientQty = qty.getAttribute("data-original");
					ingredientQty = math.fraction(ingredientQty);
					let newIngredientQty = eval(
						(ingredientQty / originalServe) * newServe
					);
					if (ingredientQty.d != 1) {
						newIngredientQty = math.fraction(newIngredientQty);
						newIngredientQty =
							newIngredientQty.n + "/" + newIngredientQty.d;
					}
					qty.innerText = newIngredientQty;
				});
			}
		});
	});

	// smooth scroll intoview
	Util.on("click", "a", function (e) {
		if (e.target.getAttribute("href")?.match(/^#.*$/)) {
			e.preventDefault();
			let targetID = document.querySelector(
				e.target.getAttribute("href")
			);
			targetID.scrollIntoView({
				behavior: "smooth",
			});
		}
	});

	// init ratings
	recipe_global.initRatings("body");

	// init light gallery
	recipe_global.initLightGallery(document.body);

	document.addEventListener("click", function (e) {
		// faq accordion
		handleFaqAccordion(e);
	});
}

function handleFaqAccordion(e) {
	if (e.target.matches(".dr-switch-btn")) {
		let switchButtons = e.target;
		if (switchButtons) {
			let switchStats = switchButtons.getAttribute("data-switch");
			let switchOnText = switchButtons.getAttribute("data-switch-on");
			let switchOffText = switchButtons.getAttribute("data-switch-off");
			let targetID = switchButtons.getAttribute("data-target");
			if (switchStats == "off") {
				switchButtons.setAttribute("data-switch", "on");
				switchButtons.innerText = switchOnText;
			} else {
				switchButtons.setAttribute("data-switch", "off");
				switchButtons.innerText = switchOffText;
			}
			let switchStatus = switchButtons.getAttribute("data-switch");
			let faqItems = document
				.querySelector(targetID)
				.querySelectorAll(".dr-faq-item");
			faqToggle(faqItems, switchStatus);
		}
	}

	// faq accordion
	["dr-faq-title-wrap", "dr-title"].forEach(function (cls) {
		if (e.target.classList.contains(cls)) {
			if (e.target.closest(".dr-faq-item").matches(".faq-active")) {
				e.target.closest(".dr-faq-item").classList.remove("faq-active");
			} else {
				e.target.closest(".dr-faq-item").classList.add("faq-active");
			}
		}
	});

	function faqToggle(toggleItem, switchStatus) {
		if (switchStatus == "on") {
			toggleItem.forEach(function (item) {
				item.classList.add("faq-active");
			});
		} else {
			toggleItem.forEach(function (item) {
				item.classList.remove("faq-active");
			});
		}
	}
}
