/**
 * BLOCK:
 */

import apiFetch from "@wordpress/api-fetch";
import { RecipeCard } from "../common/RecipeCard";
import { LayoutOne } from "../common/LayoutOne";
import { LayoutTwo } from "../common/LayoutTwo";
import icons from "../common/icons";

import { __ } from "@wordpress/i18n"; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const { Component, Fragment } = wp.element; // Import Componen and Fragment
const { InspectorControls } = wp.blockEditor; // Import
const { PanelBody, TextControl, SelectControl } = wp.components; // Import Setting Controls

class Delicious_Recipes_Recipe_Card extends Component {
	constructor() {
		super(...arguments);

		this.state = {
			recipes: "",
			RecipeOptions: [],
			isLoading: false,
		};

		this.preventClick = this.preventClick.bind(this);
		this.doApiRecipesRequest = this.doApiRecipesRequest.bind(this);
		this.doApiRequest = this.doApiRequest.bind(this);
	}

	preventClick(e) {
		e.preventDefault();
		return false;
	}

	doApiRecipesRequest() {
		const { recipes, RecipeOptions, isLoading } = this.state;
		const { setAttributes } = this.props;

		apiFetch({
			url: delrcp.ajaxURL + "?action=delicious_recipes_recipe_card_ajax",
		}).then((_terms) => {
			const { RecipeOptions } = _terms;
			this.setState({ RecipeOptions });
		});
	}

	doApiRequest(Recipe) {
		const { attributes, setAttributes } = this.props;
		const { recipes, isLoading } = this.state;

		if (Recipe == null) {
			Recipe = "";
		}

		this.setState({ isLoading: !isLoading });

		apiFetch({
			url:
				delrcp.ajaxURL +
				"?action=delicious_recipes_recipe_card_block_ajax&posts_in=" +
				Recipe,
		}).then((_recipes) => {
			const { found, recipes } = _recipes;

			if (true === found) {
				this.setState({ recipes, isLoading: false });
				// Set Recipe attribute to first recipe post id
				if (!Recipe) {
					setAttributes({ Recipe: recipes?.[0]?.recipe_id });
				}
			} else {
				this.setState({ recipes: "", isLoading: false });
			}
		});
	}

	componentDidMount() {
		const { attributes } = this.props;
		const { Recipe } = attributes;

		this.doApiRecipesRequest();
		this.doApiRequest(Recipe);
	}

	shouldComponentUpdate(nextProps, nextState) {
		const { Recipe } = this.props.attributes;

		if (Recipe !== nextProps.attributes.Recipe) {
			this.doApiRequest(nextProps.attributes.Recipe);
		}

		return true;
	}

	render() {
		const { attributes, setAttributes } = this.props;
		const { title, heading, Recipe, layout } = attributes;
		const { recipes, RecipeOptions, isLoading } = this.state;

		return (
			<Fragment>
				<InspectorControls>
					<PanelBody
						title={__("Recipe Card Settings", "delicious-recipes")}
					>
						<TextControl
							label={__("Title", "delicious-recipes")}
							value={title}
							onChange={(value) =>
								setAttributes({ title: value })
							}
						/>
						<SelectControl
							label={__("Heading", "delicious-recipes")}
							value={heading}
							options={[
								{ label: "H1", value: "h1" },
								{ label: "H2", value: "h2" },
								{ label: "H3", value: "h3" },
								{ label: "H4", value: "h4" },
								{ label: "H5", value: "h5" },
								{ label: "H6", value: "h6" },
								{ label: "Paragraph", value: "p" },
							]}
							onChange={(value) =>
								setAttributes({ heading: value })
							}
						/>
						<div className="components-base-control">
							<div className="components-base-control__field">
								<SelectControl
									label={__("Recipe", "delicious-recipes")}
									value={Recipe}
									options={RecipeOptions}
									onChange={(value) =>
										setAttributes({ Recipe: value })
									}
								/>
							</div>
						</div>
						<SelectControl
							label={__(
								"Default Card Layout",
								"delicious-recipes"
							)}
							value={layout}
							options={[
								{ label: "Default Layout", value: "default" },
								{ label: "Layout One", value: "layout-1" },
								{ label: "Layout Two", value: "layout-2" },
							]}
							onChange={(value) =>
								setAttributes({ layout: value })
							}
						/>
					</PanelBody>
				</InspectorControls>
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
					{recipes.length > 0 &&
						recipes.map((recipe, index) => {
							return layout && layout === "layout-1" ? (
								<LayoutOne
									key={index}
									recipe={recipe}
									preventClick={this.preventClick}
								/>
							) : layout && layout === "layout-2" ? (
								<LayoutTwo
									key={index}
									recipe={recipe}
									preventClick={this.preventClick}
								/>
							) : (
								<RecipeCard
									key={index}
									recipe={recipe}
									preventClick={this.preventClick}
								/>
							);
						})}
				</div>
				{Object.entries(recipes).length < 1
					? __(
							"Please Select the recipe for recipe card.",
							"delicious-recipes"
					  )
					: ""}
			</Fragment>
		);
	}
}

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType("delicious-recipes/recipe-card", {
	title: __("Recipe Card", "delicious-recipes"),
	description: __("A block to show recipe card.", "delicious-recipes"),
	icon: icons.recipecard,
	category: "delicious-recipes",
	keywords: [
		__("recipecard", "delicious-recipes"),
		__("delicious", "delicious-recipes"),
		__("recipes", "delicious-recipes"),
	],
	attributes: {
		// default values
		title: {
			type: "string",
			default: __("Recipe Details", "delicious-recipes"),
		},
		heading: {
			type: "string",
			default: "h2",
		},
		Recipe: {
			type: "string",
			default: "",
		},
		layout: {
			type: "string",
			default: "default",
		},
	},
	example: {
		attributes: {
			content: __("Content of the block", "delicious-recipes"),
		},
		innerBlocks: [],
	},
	edit: Delicious_Recipes_Recipe_Card,
	save: function ({ attributes }) {
		return null;
	},
}); // END Register GBSS Boxes Block
