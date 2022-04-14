function updateIngredientsScale() {
	$ = jQuery;
	let searchParams = new URLSearchParams(window.location.search);
	var new_servings =
		searchParams.has("recipe_servings") &&
		searchParams.get("recipe_servings");
	new_servings = math.number(new_servings);
	// console.log('New Servings', new_servings);

	var recipe = print_props.recipe;

	var original_servings = print_props.original_servings;
	original_servings = math.number(original_servings);
	// console.log('Original Servings', original_servings);

	$("#dr-servings").text(new_servings);

	$(".ingredient_quantity[data-recipe=" + recipe + "]").each(function (
		index
	) {
		var quantity = $(this).data("original");
		if (!isFinite(quantity)) {
			try {
				quantity = math.fraction(quantity);
			} catch (e) {
				// console.log(e);
				return;
			}
			// console.log(
			// 	"Fractional Quantity",
			// 	math.format(quantity, { fraction: "ratio" })
			// );

			var new_quantity = math.eval(
				(quantity / original_servings) * new_servings
			);
			//console.log('New Decimal', new_quantity);

			new_quantity = math.fraction(new_quantity);
			//console.log('New Fractional', new_quantity);

			if (new_quantity.n == new_quantity.d || new_quantity.d == 1) {
				jQuery(this).text(new_quantity.n);
				//console.log('New Quantity =', jQuery(this).text());
			} else if (new_quantity.n > new_quantity.d) {
				var i = parseInt(new_quantity.n / new_quantity.d);
				new_quantity.n -= i * new_quantity.d;
				jQuery(this).text(
					i + " " + " " + new_quantity.n + "/" + new_quantity.d
				);
				//console.log('New Quantity >', jQuery(this).text());
			} else {
				jQuery(this).text(
					math.format(new_quantity, { fraction: "ratio" })
				);
				//console.log('New Quantity', jQuery(this).text());
			}
		} else {
			try {
				quantity = math.number(quantity);
			} catch (e) {
				// console.log(e);
				return;
			}
			// console.log("Numeric Quantity", quantity);

			var new_quantity = math.eval(
				new_servings * (quantity / original_servings)
			);
			new_quantity = math.format(new_quantity, {
				fraction: "decimal",
				precision: 4,
			});
			0 != new_quantity && jQuery(this).text(new_quantity);
			// console.log("New Quantity", jQuery(this).text());
		}
	});
}
jQuery(document).ready(function ($) {
	updateIngredientsScale();
});
