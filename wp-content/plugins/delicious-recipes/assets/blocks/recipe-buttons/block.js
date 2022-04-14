/**
 * BLOCK: recipe-buttons
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

/* External dependencies */
import { __ } from '@wordpress/i18n';

/* Internal dependencies */
import icons from "../common/icons";
import RecipeButtons from "./RecipeButtons";

/* WordPress dependencies */
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

/**
 * Register: Recipe Buttons Gutenberg Block.
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
registerBlockType( 'delicious-recipes/block-recipe-buttons', {
    title: __( 'Recipe Buttons', 'delicious-recipes' ),
    description: __( 'A Jump to Recipe, Jump to Video and Print Recipe button.', 'delicious-recipes' ),
    icon: icons.recipebuttons,
    category: 'delicious-recipes',
    supports: {
        multiple: false,
        html: false,
    },
    keywords: [
        __( 'recipe card', 'delicious-recipes' ),
        __( 'block recipe card', 'delicious-recipes' ),
        __( 'recipes button', 'delicious-recipes' ),
    ],

    /**
     * The edit function describes the structure of your block in the context of the editor.
     * This represents what the editor will render when the block is used.
     *
     * The "edit" property must be a valid function.
     *
     * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
     */
    edit: ({ attributes, setAttributes, className }) => {
		return <RecipeButtons {...{ attributes, setAttributes, className }} />;
	},

	save() {
		// Rendering in PHP
		return null;
	},

} );

