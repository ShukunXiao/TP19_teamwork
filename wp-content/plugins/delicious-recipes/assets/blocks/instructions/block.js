/**
 * BLOCK: block-instructions
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

/* Internal dependencies */
import Direction from "../recipe-card-dynamic/components/Direction";
import { generateId } from "../../helpers/generateId";
import icons from "../common/icons";

/* External dependencies */
import { __ } from "@wordpress/i18n";
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

/**
 * Register: Instructions Gutenberg Block.
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
registerBlockType("delicious-recipes/dynamic-instructions", {
	title: __("Recipe Instructions", "delicious-recipes"),
	description: __("A block to list the recipe instructions.", "delicious-recipes"),
	icon: icons.instructions,
	category: "delicious-recipes",
	supports: {
		multiple: true,
	},
	keywords: [
		__("directions", "delicious-recipes"),
		__("instructions", "delicious-recipes"),
		__("recipe", "delicious-recipes"),
	],
	example: {
		attributes: {
			steps: [
				{
					id: generateId("direction-step"),
					isGroup: false,
					text: [
						"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam fringilla nunc id nibh rutrum, tristique finibus quam interdum.",
					],
				},
				{
					id: generateId("direction-step"),
					isGroup: false,
					text: [
						"Praesent feugiat dui eu pretium eleifend. In non tempus est. Praesent ullamcorper sapien vitae viverra imperdiet.",
					],
				},
				{
					id: generateId("direction-step"),
					isGroup: true,
					text: ["Section Title here"],
				},
				{
					id: generateId("direction-step"),
					isGroup: false,
					text: ["Aenean nec diam a augue efficitur venenatis."],
				},
				{
					id: generateId("direction-step"),
					isGroup: false,
					text: [
						"Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.",
					],
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
		const steps = attributes.steps ? attributes.steps.slice() : [];

		// Populate deprecated attribute 'content'
		// Backward compatibility
		if (attributes.content && attributes.content.length > 0) {
			const content = attributes.content;

			if (steps.length === 0) {
				for (let i = 0; i < content.length; i++) {
					if (!isUndefined(content[i].props)) {
						steps.push({
							id: generateId("direction-step"),
							text: content[i].props.children,
						});
					}
				}

				setAttributes({ steps });
			}
		}

		// Because setAttributes is quite slow right after a block has been added we fake having a three steps.
		if (!steps || steps.length === 0) {
			attributes.steps = [
				{
					id: generateId("direction-step"),
					text: [],
				},
				{
					id: generateId("direction-step"),
					text: [],
				},
				{
					id: generateId("direction-step"),
					text: [],
				},
			];
		}

		return (
			<Direction
				generateId={generateId}
				{...{ attributes, setAttributes, className }}
			/>
		);
	},

	save: function ({ attributes }) {
		return null;
	},
}); // END Register GBSS Boxes Block
