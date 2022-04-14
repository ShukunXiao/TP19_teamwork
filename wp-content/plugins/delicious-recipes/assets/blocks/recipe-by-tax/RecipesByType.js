import { RecipeGrid } from "../common/RecipeGrid.js";
import { RecipeList } from "../common/RecipeList.js";
const { Fragment } = wp.element;

import { useEffect, useState } from "@wordpress/element";
import apiFetch from "@wordpress/api-fetch";

import { __ } from "@wordpress/i18n";

const PreventClick = (e) => {
	e.preventDefault();
	return false;
};

function RecipesByType({ attributes }) {
	const {
		title,
		heading,
		layout,
		recipeNumber,
		recipeType,
		tax,
	} = attributes;
	const [Recipes, setRecipes] = useState([]);
	const [isLoading, setisLoading] = useState(true);

	useEffect(() => {
		apiFetch({
			url: `${delrcp.ajaxURL}?action=delicious_recipes_recipe_type_block&post_number=${recipeNumber}&taxonomy=${tax}&term=${recipeType}`,
		}).then((recipes_fetched) => {
			setRecipes(recipes_fetched.recipes);
			setisLoading(false);
		});
	}, [tax, recipeType, recipeNumber]);

	return (
		<Fragment>
			{"h1" === heading && <h1 className="widget-title">{title}</h1>}
			{"h2" === heading && <h2 className="widget-title">{title}</h2>}
			{"h3" === heading && <h3 className="widget-title">{title}</h3>}
			{"h4" === heading && <h4 className="widget-title">{title}</h4>}
			{"h5" === heading && <h5 className="widget-title">{title}</h5>}
			{"h6" === heading && <h6 className="widget-title">{title}</h6>}
			{"p" === heading && <p className="widget-title">{title}</p>}
			{isLoading && (
				<span className="loading">
					{__("Loading...", "delicious-recipes")}
				</span>
			)}
			<div className="te-post-wrap-outer">
				{Recipes.map((recipe, index) =>
					"list-view" == layout ? (
						<RecipeList
							key={index}
							recipe={recipe}
							preventClick={PreventClick}
						/>
					) : (
						<RecipeGrid
							key={index}
							recipe={recipe}
							preventClick={PreventClick}
						/>
					)
				)}
			</div>
			{Recipes.length < 0
				? __("Recipes not found.", "delicious-recipes")
				: ""}
		</Fragment>
	);
}

export default RecipesByType;
