import Select from "react-select";
import { __ } from "@wordpress/i18n"; // Import __() from wp.i18n

const { InspectorControls } = wp.blockEditor; // Import
const { PanelBody, TextControl, SelectControl } = wp.components; // Import Setting Controls

export default ({attributes, setAttributes, RecipeOptions}) => {
    const {
		title,
		heading,
		layout,
        Recipe
	} = attributes;

    return (
        <InspectorControls>
            <PanelBody
                title={__("Handpicked Recipes Settings", "delicious-recipes")}
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
                        <label className="components-base-control__label">
                            {__("Recipes", "delicious-recipes")}
                        </label>
                        <Select
                            label={__("Recipes", "delicious-recipes")}
                            value={Recipe}
                            options={RecipeOptions}
                            isMulti="true"
                            onChange={(value) =>
                                setAttributes({ Recipe: value })
                            }
                        />
                    </div>
                </div>
                <SelectControl
                    label={__("Layout", "delicious-recipes")}
                    value={layout}
                    options={[
                        { label: __("Grid View", "delicious-recipes"), value: "grid-view" },
                        { label: __("List View", "delicious-recipes"), value: "list-view" },
                    ]}
                    onChange={(value) =>
                        setAttributes({ layout: value })
                    }
                />
            </PanelBody>
        </InspectorControls>
    )
}
