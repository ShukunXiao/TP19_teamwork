import { PanelBody, SelectControl, TextControl } from "@wordpress/components";
import { useEffect, useState } from "@wordpress/element";
import apiFetch from "@wordpress/api-fetch";

import { __ } from "@wordpress/i18n";
const { InspectorControls } = wp.blockEditor;

function BlockInspectorControls({ attributes, setAttributes, className }) {
	const [Terms, setTerms] = useState([]);

	const {
		title,
		heading,
		layout,
		recipeNumber,
		recipeType,
		tax,
	} = attributes;

	useEffect(() => {
		apiFetch({
			url: `${delrcp.ajaxURL}?action=delicious_recipes_recipe_type_taxomomy&tax=${tax}`,
		}).then((fetched_terms) => {
			setTerms(fetched_terms.types);
		});
	}, [tax]);

	return (
		<InspectorControls>
			<PanelBody
				title={__("Recipes by Taxonomy Settings", "delicious-recipes")}
			>
				<TextControl
					label={__("Title", "delicious-recipes")}
					value={title}
					onChange={(value) => setAttributes({ title: value })}
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
					onChange={(value) => setAttributes({ heading: value })}
				/>
				<SelectControl
					label={__("Taxonomy", "delicious-recipes")}
					value={tax}
					options={[
						{
							label: __("Courses", "delicious-recipes"),
							value: "recipe-course",
						},
						{
							label: __("Cuisines", "delicious-recipes"),
							value: "recipe-cuisine",
						},
						{
							label: __("Cooking Methods", "delicious-recipes"),
							value: "recipe-cooking-method",
						},
						{
							label: __("Tags", "delicious-recipes"),
							value: "recipe-tag",
						},
						{
							label: __("Recipe Keys", "delicious-recipes"),
							value: "recipe-key",
						},
					]}
					onChange={(value) =>
						setAttributes({ tax: value, recipeType: "" })
					}
				/>
				<SelectControl
					label={__("Term", "delicious-recipes")}
					value={recipeType}
					options={Terms}
					onChange={(value) => setAttributes({ recipeType: value })}
				/>
				<SelectControl
					label={__("Layout", "delicious-recipes")}
					value={layout}
					options={[
						{
							label: __("Grid View", "delicious-recipes"),
							value: "grid-view",
						},
						{
							label: __("List View", "delicious-recipes"),
							value: "list-view",
						},
					]}
					onChange={(value) => setAttributes({ layout: value })}
				/>
				<TextControl
					label={__("No. of recipe to show", "delicious-recipes")}
					type="number"
					min={1}
					value={recipeNumber}
					onChange={(value) => setAttributes({ recipeNumber: value })}
				/>
			</PanelBody>
		</InspectorControls>
	);
}

export default BlockInspectorControls;
