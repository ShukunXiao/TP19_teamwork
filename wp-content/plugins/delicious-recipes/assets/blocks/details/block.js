/**
 * BLOCK: block-details
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

/* Internal dependencies */
import Detail from "../recipe-card-dynamic/components/Detail";
import { generateId } from "../../helpers/generateId";
import icons from "../common/icons";
import Inspector from "./Inspector";

/* External dependencies */
import { __ } from "@wordpress/i18n";
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

/**
 * Register: Details Gutenberg Block.
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
registerBlockType("delicious-recipes/dynamic-details", {
	title: __("Recipe Details", "delicious-recipes"),
	description: __("A block to list the recipe details.", "delicious-recipes"),
	icon: icons.details,
	category: "delicious-recipes",
	supports: {
		multiple: false,
	},
	keywords: [
		__("details", "delicious-recipes"),
		__("delicious", "delicious-recipes"),
		__("recipes", "delicious-recipes"),
	],
	example: {
		attributes: {
			course: [__("Main", "delicious-recipes")],
			cuisine: [__("Italian", "delicious-recipes")],
			difficulty: [__("Medium", "delicious-recipes")],
			details: [
				{
					id: generateId("detail-item"),
					icon: "time",
					label: __("Prep time", "delicious-recipes"),
				},
				{
					id: generateId("detail-item"),
					icon: "time",
					label: __("Cooking time", "delicious-recipes"),
				},
				{
					id: generateId("detail-item"),
					icon: "yield",
					label: __("Servings", "delicious-recipes"),
				},
				{
					id: generateId("detail-item"),
					icon: "calories",
					label: __("Calories", "delicious-recipes"),
				},
			],
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
		// Because setAttributes is quite slow right after a block has been added we fake having a single detail.
		if (!attributes.details || attributes.details.length === 0) {
			attributes.details = [
				{
					id: generateId("detail-item"),
					icon: "time",
					label: __("Prep time", "delicious-recipes"),
				},
				{
					id: generateId("detail-item"),
					icon: "time",
					label: __("Cooking time", "delicious-recipes"),
				},
				{
					id: generateId("detail-item"),
					icon: "time",
					label: __("Resting time", "delicious-recipes"),
				},
				{
					id: generateId("detail-item"),
					icon: "time",
					label: __("Total time", "delicious-recipes"),
				},
				{
					id: generateId("detail-item"),
					icon: "yield",
					label: __("Servings", "delicious-recipes"),
				},
				{
					id: generateId("detail-item"),
					icon: "calories",
					label: __("Calories", "delicious-recipes"),
				},
			];
		}

		return (
			<div className="dr-details-block">
				<Detail
					generateId={generateId}
					{...{ attributes, setAttributes, className }}
				/>
				<Inspector
					{...{ attributes, setAttributes, className }}
				/>
			</div>
		);
	},

	save() {
		// Rendering in PHP
		return null;
	},
}); // END Register GBSS Boxes Block
