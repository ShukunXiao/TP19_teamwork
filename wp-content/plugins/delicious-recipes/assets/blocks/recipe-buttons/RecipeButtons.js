/* WordPress dependencies */
import get from "lodash/get";
import { __ } from "@wordpress/i18n";
import icons from "../common/icons";
const { Component, Fragment } = wp.element;
const { InspectorControls } = wp.blockEditor;
const {	BaseControl, ToggleControl, PanelBody, TextControl } = wp.components;

/**
 * A Recipe Buttons block.
 */
export default class RecipeButtons extends Component {
	/**
	 * Constructs a RecipeButtons editor component.
	 *
	 * @param {Object} props This component's properties.
	 *
	 * @returns {void}
	 */
	constructor(props) {
		super(props);
        this.onChangeSettings = this.onChangeSettings.bind(this);
    }
    
    onChangeSettings(newValue, param, index = 0) {
        const {
            setAttributes,
            attributes: { settings },
        } = this.props;
        const newSettings = settings ? settings.slice() : [];

        if (!get(newSettings, index)) {
            newSettings[index] = {};
        }

        newSettings[index][param] = newValue;

        setAttributes({ settings: newSettings });
    }

	render() {
		const { attributes, setAttributes } = this.props;
        const { 
            id,
            jumptorecipeTitle,
            jumptovideoTitle,
            printrecipeTitle,
            settings: {
                0: {
                    jump_to_recipe_btn,
                    jump_to_video_btn,
                    print_recipe_btn,
                }
            }
        } = attributes;

        return (
            <Fragment>
                <div id={id}>
                    {jump_to_recipe_btn && (
                        <div className="dr-buttons dr-jump-to-recipe-btn">
                            {jumptorecipeTitle}
                            {icons.goto}
                        </div>
                    )}
                    {jump_to_video_btn && (
                        <div className="dr-buttons dr-jump-to-video-btn">
                            {icons.playbutton}
                            {jumptovideoTitle}
                        </div>
                    )}
                    {print_recipe_btn && (
                        <div className="dr-buttons dr-print-recipe-btn"
                            style={{
                                backgroundColor: delrcp.setting_options.primaryColor,
                            }}
                        >
                            {icons.print}
                            {printrecipeTitle}
                        </div>
                    )}
                </div>
                <InspectorControls>
                    <PanelBody
                        className="delicious-recipes-settings"
                        initialOpen={true}
                        title={__("Recipe Buttons Settings", "delicious-recipes")}
                    >
                        <Fragment>
                            <BaseControl
                                id={`${id}-jump-recipe-btn`}
                                label={__("Jump to Recipe", "delicious-recipes")}
                            >
                                <ToggleControl
                                    label={__(
                                        "Display Jump to Recipe",
                                        "delicious-recipes"
                                    )}
                                    checked={jump_to_recipe_btn}
                                    onChange={(display) =>
                                        this.onChangeSettings(display, "jump_to_recipe_btn")
                                    }
                                />
                                {jump_to_recipe_btn && (
                                    <TextControl
                                        id={`${id}-jump-recipe-label`}
                                        instanceid={`${id}-jump-recipe-label`}
                                        type="text"
                                        label={__(
                                            "Jump to Recipes Label",
                                            "delicious-recipes"
                                        )}
                                        placeholder={__(
                                            "Jump to Recipes",
                                            "delicious-recipes"
                                        )}
                                        value={jumptorecipeTitle}
                                        onChange={(newValue) =>
                                            setAttributes({ jumptorecipeTitle: newValue })
                                        }
                                    />
                                )}
                            </BaseControl>
                            <BaseControl
                                id={`${id}-jump-video-btn`}
                                label={__("Jump to Video", "delicious-recipes")}
                            >
                                <ToggleControl
                                    label={__(
                                        "Display Jump to Video",
                                        "delicious-recipes"
                                    )}
                                    checked={jump_to_video_btn}
                                    onChange={(display) =>
                                        this.onChangeSettings(display, "jump_to_video_btn")
                                    }
                                />
                                {jump_to_video_btn && (
                                    <TextControl
                                        id={`${id}-jump-video-label`}
                                        instanceid={`${id}-jump-video-label`}
                                        type="text"
                                        label={__(
                                            "Jump to Video Label",
                                            "delicious-recipes"
                                        )}
                                        placeholder={__(
                                            "Jump to Video",
                                            "delicious-recipes"
                                        )}
                                        value={jumptovideoTitle}
                                        onChange={(newValue) =>
                                            setAttributes({ jumptovideoTitle: newValue })
                                        }
                                    />
                                )}
                            </BaseControl>
                            <BaseControl
                                id={`${id}-print-btn`}
                                label={__("Print Button", "delicious-recipes")}
                            >
                                <ToggleControl
                                    label={__(
                                        "Display Print Button",
                                        "delicious-recipes"
                                    )}
                                    checked={print_recipe_btn}
                                    onChange={(display) =>
                                        this.onChangeSettings(display, "print_recipe_btn")
                                    }
                                />
                                {print_recipe_btn && (
                                    <TextControl
                                        id={`${id}-print-recipe-label`}
                                        instanceid={`${id}-print-recipe-label`}
                                        type="text"
                                        label={__(
                                            "Print Recipe Label",
                                            "delicious-recipes"
                                        )}
                                        placeholder={__(
                                            "Print Recipe",
                                            "delicious-recipes"
                                        )}
                                        value={printrecipeTitle}
                                        onChange={(newValue) =>
                                            setAttributes({ printrecipeTitle: newValue })
                                        }
                                    />
                                )}
                            </BaseControl>
                        </Fragment>
                    </PanelBody>
                </InspectorControls>
            </Fragment>
        );
	}
}