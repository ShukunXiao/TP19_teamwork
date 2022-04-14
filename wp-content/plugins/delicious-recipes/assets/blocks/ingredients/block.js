/**
 * BLOCK: block-ingredients
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

/* Internal dependencies */
import Ingredient from "../recipe-card-dynamic/components/Ingredient";
import { generateId } from "../../helpers/generateId";
import icons from "../common/icons";

/* External dependencies */
import { __ } from "@wordpress/i18n";
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

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
registerBlockType("delicious-recipes/dynamic-ingredients", {
	title: __("Recipe Ingredients", "delicious-recipes"),
	description: __("A block to list the recipe ingredients.", "delicious-recipes"),
	icon: icons.ingredients,
	category: "delicious-recipes",
	supports: {
		multiple: true,
	},
	keywords: [
		__("ingredients", "delicious-recipes"),
		__("delicious", "delicious-recipes"),
		__("recipes", "delicious-recipes"),
	],
	example: {
		attributes: {
			items: [
				{
					id: generateId("ingredient-item"),
					isGroup: false,
					name: ["Lorem ipsum dolor sit amet"],
				},
				{
					id: generateId("ingredient-item"),
					isGroup: false,
					name: ["Praesent feugiat dui eu pretium eleifend"],
				},
				{
					id: generateId("ingredient-item"),
					isGroup: true,
					name: ["Section Title here"],
				},
				{
					id: generateId("ingredient-item"),
					isGroup: false,
					name: ["Aenean nec diam a augue efficitur venenatis"],
				},
				{
					id: generateId("ingredient-item"),
					isGroup: false,
					name: ["Pellentesque habitant morbi"],
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
		const items = attributes.items ? attributes.items.slice() : [];

		// Populate deprecated attribute 'content'
		// Backward compatibility
		if (attributes.content && attributes.content.length > 0) {
			const content = attributes.content;

			if (items.length === 0) {
				for (let i = 0; i < content.length; i++) {
					if (!isUndefined(content[i].props)) {
						items.push({
							id: generateId("ingredient-item"),
							name: content[i].props.children,
						});
					}
				}

				setAttributes({ items });
			}
		}

		// Because setAttributes is quite slow right after a block has been added we fake having a four ingredients.
		if (!items || items.length === 0) {
			attributes.items = [
				{
					id: generateId("ingredient-item"),
					name: [],
				},
				{
					id: generateId("ingredient-item"),
					name: [],
				},
				{
					id: generateId("ingredient-item"),
					name: [],
				},
				{
					id: generateId("ingredient-item"),
					name: [],
				},
			];
		}

		return (
			<Ingredient
				generateId={generateId}
				{...{ attributes, setAttributes, className }}
			/>
		);
	},

	save: function ({ attributes }) {
		return null;
	},
}); // END Register GBSS Boxes Block
