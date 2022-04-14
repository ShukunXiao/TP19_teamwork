/**
 * BLOCK: block-nutrition
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

/* Internal dependencies */
import Nutrition from "./Nutrition";
import icons from "../common/icons";

/* External dependencies */
import { __ } from "@wordpress/i18n";
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

/**
 * Register: Nutrition Gutenberg Block.
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
registerBlockType("delicious-recipes/block-nutrition", {
	title: __("Recipe Nutrition", "delicious-recipes"),
	description: __("A block to display nutrition facts for your recipe.", "delicious-recipes"),
	icon: icons.nutrition,
	category: "delicious-recipes",
	supports: {
		multiple: false,
	},
	keywords: [
		__("nutrition", "delicious-recipes"),
		__("delicious", "delicious-recipes"),
		__("recipes", "delicious-recipes"),
	],

	/**
     * The edit function describes the structure of your block in the context of the editor.
     * This represents what the editor will render when the block is used.
     *
     * The "edit" property must be a valid function.
     *
     * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
     */
    edit: ( { attributes, setAttributes, className, clientId } ) => {
        return <Nutrition { ...{ attributes, setAttributes, className, clientId } } />;
    },

    save() {
        // Rendering in PHP
        return null;
	},
	
}); // END Register
