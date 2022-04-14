/**
 * BLOCK: featured
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

/* Internal dependencies */
import RecipesByType from "./RecipesByType";
import BlockInspectorControls from "./BlockInspectorControls";
import icons from "../common/icons";

import { __ } from "@wordpress/i18n";

const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

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
registerBlockType("delicious-recipes/tax-type", {
	title: __("Recipes by Taxonomy", "delicious-recipes"),
	description: __(
		"A block to list the recipes by taxonomy.",
		"delicious-recipes"
	),
	icon: icons.taxonomy,
	category: "delicious-recipes",
	keywords: [
		__("taxonomy", "delicious-recipes"),
		__("delicious", "delicious-recipes"),
		__("recipes", "delicious-recipes"),
	],
	attributes: {
		// default values
		title: {
			type: "string",
			default: __("Recipes by Type", "delicious-recipes"),
		},
		heading: {
			type: "string",
			default: "h2",
		},
		tax: {
			type: "string",
			default: "recipe-course",
		},
		recipeType: {
			type: "string",
			default: "",
		},
		layout: {
			type: "string",
			default: "grid-view",
		},
		recipeNumber: {
			type: "string",
			default: "2",
		},
	},
	example: {
		attributes: {
			content: __("Content of the block", "delicious-recipes"),
		},
		innerBlocks: [],
	},
	edit: ({ attributes, setAttributes, className }) => {
		return (
			<>
				<BlockInspectorControls
					{...{ attributes, setAttributes, className }}
				/>
				<RecipesByType {...{ attributes }} />
			</>
		);
	},
	save: function ({ attributes }) {
		return null;
	},
}); // END Register GBSS Boxes Block
