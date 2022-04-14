/**
 * BLOCK: block-recipe-card
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

/* External dependencies */
import { __ } from "@wordpress/i18n";
/* Internal dependencies */
import RecipeCard from "./components/RecipeCard";
import icons from "./../common/icons";

/* WordPress dependencies */
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const { pluginURL } = delrcp;

/**
 * Register: Ingredients Gutenberg Block.
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
registerBlockType("delicious-recipes/dynamic-recipe-card", {
	title: __("Dynamic Recipe Card", "delicious-recipes"),
	description: __(
		"Display a Recipe Card box with recipe metadata.",
		"delicious-recipes"
	),
	icon: {
		// // Specifying a background color to appear with the icon e.g.: in the inserter.
		// background: '#2EA55F',
		// Specifying a color for the icon (optional: if not set, a readable color will be automatically defined)
		foreground: "#2EA55F",
		// Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
		src: icons.dynamiccard,
	},
	category: "delicious-recipes",
	supports: {
		multiple: false,
	},
	keywords: [
		__("dynamic recipe card", "delicious-recipes"),
		__("block recipe card", "delicious-recipes"),
		__("delicious recipes", "delicious-recipes"),
	],
	example: {
		attributes: {
			recipeTitle: __(
				"Your recipe title goes here",
				"delicious-recipes"
			),
			hasImage: true,
			image: {
				id: 0,
				url: pluginURL + "/assets/images/dummy-recipe-img.jpg",
			},
			course: [__("Main", "delicious-recipes")],
			cuisine: [__("Italian", "delicious-recipes")],
			method: [__("Frying", "delicious-recipes")],
			difficulty: [__("Medium", "delicious-recipes")],
		},
	},

	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	edit: ({ attributes, setAttributes, className }) => {
		return <RecipeCard {...{ attributes, setAttributes, className }} />;
	},

	save() {
		// Rendering in PHP
		return null;
	},
});
