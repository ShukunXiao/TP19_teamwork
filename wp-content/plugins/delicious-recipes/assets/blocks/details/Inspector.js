/* External dependencies */
import { __ } from "@wordpress/i18n";
import get from "lodash/get";
import isUndefined from "lodash/isUndefined";
import isEmpty from "lodash/isEmpty";
import toString from "lodash/toString";

/* Internal dependencies */
import { stripHTML } from "./../../helpers/stringHelpers";
import { getNumberFromString } from "./../../helpers/convertMinutesToHours";

/* WordPress dependencies */
const { Component, renderToString, Fragment } = wp.element;
const { InspectorControls } = wp.blockEditor;
const {
	PanelBody,
	TextControl,
	ToggleControl,
	SelectControl,
	PanelRow,
	Button,
} = wp.components;

/**
 * Inspector controls
 */
export default class Inspector extends Component {
	/**
	 * Constructs a Inspector editor component.
	 *
	 * @param {Object} props This component's properties.
	 *
	 * @returns {void}
	 */
	constructor(props) {
		super(props);

		this.onChangeDetail = this.onChangeDetail.bind(this);
		this.onChangeSettings = this.onChangeSettings.bind(this);

		this.state = {
			isCalculatedTotalTime: false,
			isCalculateBtnClick: false,
		};
	}

	componentDidMount() {
		this.calculateTotalTime();
	}

	componentDidUpdate() {
		if (!this.state.isCalculatedTotalTime) {
			this.calculateTotalTime();
		}
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

	onChangeDetail(newValue, index, field) {
		const {
			setAttributes,
			attributes: { details },
		} = this.props;
		const newDetails = details ? details.slice() : [];

		const id = get(newDetails, [index, "id"]);
		const icon = get(newDetails, [index, "icon"]);

		if (!get(newDetails, index)) {
			newDetails[index] = {};
		}

		if (!id) {
			newDetails[index].id = uniqueId(
				`detail-item-${new Date().getTime()}`
			);
		}

		if ("icon" === field) {
			newDetails[index].icon = newValue;
		} else if (!icon) {
			newDetails[index].icon = "restaurant-utensils";
		}

		if ("label" === field) {
			newDetails[index][field] = newValue;
			newDetails[index].jsonLabel = stripHTML(renderToString(newValue));
		}
		if ("value" === field) {
			newDetails[index][field] = newValue;
			newDetails[index].jsonValue = stripHTML(renderToString(newValue));
		}
		if ("unit" === field) {
			newDetails[index][field] = newValue;
			newDetails[index].jsonUnit = stripHTML(renderToString(newValue));
		}
		setAttributes({ details: newDetails });
	}

	calculateTotalTime() {
		// We already have value for total time, in this case we don't need to recalculate them
		if (this.state.isCalculatedTotalTime) {
			return;
		}

		const { details, 
			settings: {
				0: {
					displayPrepTime,
					displayCookingTime,
					displayRestTime,
				},
			} } = this.props.attributes;
		const index = 3; // Total Time index in details object array
		const prepTime = displayPrepTime ? getNumberFromString(get(details, [0, "value"])) : 0;
		const cookTime = displayCookingTime ? getNumberFromString(get(details, [1, "value"])) : 0;
		const restingTime = displayRestTime ? getNumberFromString(get(details, [2, "value"])) : 0;

		let totalTime = prepTime + cookTime + restingTime;

		const totalTimeValue = get(details, [index, "value"]);

		if (
			!this.state.isCalculateBtnClick &&
			!isUndefined(totalTimeValue) &&
			!isEmpty(totalTimeValue) &&
			0 != totalTimeValue
		) {
			this.setState({
				isCalculatedTotalTime: true,
				isCalculateBtnClick: false,
			});
			return;
		}

		if (
			( 
				"" != prepTime ||
				"" != cookTime ||
				"" != restingTime 
			) &&
			totalTime > 0 &&
			totalTime != totalTimeValue
		) {
			this.onChangeDetail(toString(totalTime), index, "value");
			this.setState({
				isCalculatedTotalTime: true,
				isCalculateBtnClick: false,
			});
		}
	}

	/**
	 * Renders this component.
	 *
	 * @returns {Component} The Details block settings.
	 */
	render() {
		const { attributes, setAttributes } = this.props;

		const {
			id,
			course,
			cuisine,
			method,
			recipeKey,
			difficulty,
			difficultyTitle,
			season,
			seasonTitle,
			keywords,
			details,
			settings: {
				0: {
					displayCourse,
					displayCuisine,
					displayCookingMethod,
					displayRecipeKey,
					displayDifficulty,
					displayServings,
					displayPrepTime,
					displayCookingTime,
					displayRestTime,
					displayTotalTime,
					displayCalories,
					displayBestSeason,
				},
			},
		} = attributes;

		const coursesToken = [
			__("Appetizer & Snacks", "delicious-recipes"),
			__("Breakfast & Brunch", "delicious-recipes"),
			__("Dessert", "delicious-recipes"),
			__("Drinks", "delicious-recipes"),
			__("Main Course", "delicious-recipes"),
			__("Salad", "delicious-recipes"),
			__("Soup", "delicious-recipes"),
		];

		const cuisinesToken = [
			__("American", "delicious-recipes"),
			__("Chinese", "delicious-recipes"),
			__("French", "delicious-recipes"),
			__("Indian", "delicious-recipes"),
			__("Italian", "delicious-recipes"),
			__("Japanese", "delicious-recipes"),
			__("Mediterranean", "delicious-recipes"),
			__("Mexican", "delicious-recipes"),
			__("Southern", "delicious-recipes"),
			__("Thai", "delicious-recipes"),
			__("Other world cuisine", "delicious-recipes"),
		];

		const keywordsToken = [];

		return (
			<InspectorControls key="inspector">
				<PanelBody
					initialOpen={true}
					title={__("Details Settings", "delicious-recipes")}
					className="delicious-recipes-settings"
				>
					<ToggleControl
						label={__("Display Difficulty", "delicious-recipes")}
						checked={displayDifficulty}
						onChange={(display) =>
							this.onChangeSettings(display, "displayDifficulty")
						}
					/>
					<PanelRow>
						{displayDifficulty && (
							<Fragment>
								<TextControl
									id={`${id}-difficulty-label`}
									instanceid={`${id}-difficulty-label`}
									type="text"
									label={__(
										"Difficulty Label",
										"delicious-recipes"
									)}
									placeholder={__(
										"Difficulty",
										"delicious-recipes"
									)}
									value={difficultyTitle}
									onChange={(value) =>
										setAttributes({
											difficultyTitle: value,
										})
									}
								/>
								<SelectControl
									label={__(
										"Difficulty",
										"delicious-recipes"
									)}
									value={difficulty}
									options={[
										{
											label: __("Beginner", "delicious-recipes"),
											value: "beginner",
										},
										{
											label: __("Intermediate", "delicious-recipes"),
											value: "intermediate",
										},
										{
											label: __("Advanced", "delicious-recipes"),
											value: "advanced",
										},
									]}
									onChange={(value) =>
										setAttributes({ difficulty: value })
									}
								/>
							</Fragment>
						)}
					</PanelRow>
					<ToggleControl
						label={__(
							"Display Preparation Time",
							"delicious-recipes"
						)}
						checked={displayPrepTime}
						onChange={(display) =>
							this.onChangeSettings(display, "displayPrepTime")
						}
					/>
					<PanelRow>
						{displayPrepTime && (
							<Fragment>
								<TextControl
									id={`${id}-preptime-label`}
									instanceid={`${id}-preptime-label`}
									type="text"
									label={__(
										"Prep Time Label",
										"delicious-recipes"
									)}
									placeholder={__(
										"Prep Time",
										"delicious-recipes"
									)}
									value={get(details, [0, "label"])}
									onChange={(newValue) =>
										this.onChangeDetail(
											newValue,
											0,
											"label"
										)
									}
								/>
								<TextControl
									id={`${id}-preptime-value`}
									instanceid={`${id}-preptime-value`}
									type="number"
									label={__(
										"Prep Time Value",
										"delicious-recipes"
									)}
									value={get(details, [0, "value"])}
									onChange={(newValue) =>
										this.onChangeDetail(
											newValue,
											0,
											"value"
										)
									}
								/>
								<span>{get(details, [0, "unit"])}</span>
							</Fragment>
						)}
					</PanelRow>
					<ToggleControl
						label={__("Display Cooking Time", "delicious-recipes")}
						checked={displayCookingTime}
						onChange={(display) =>
							this.onChangeSettings(display, "displayCookingTime")
						}
					/>
					<PanelRow>
						{displayCookingTime && (
							<Fragment>
								<TextControl
									id={`${id}-cookingtime-label`}
									instanceid={`${id}-cookingtime-label`}
									type="text"
									label={__(
										"Cook Time Label",
										"delicious-recipes"
									)}
									placeholder={__(
										"Cooking Time",
										"delicious-recipes"
									)}
									value={get(details, [1, "label"])}
									onChange={(newValue) =>
										this.onChangeDetail(
											newValue,
											1,
											"label"
										)
									}
								/>
								<TextControl
									id={`${id}-cookingtime-value`}
									instanceid={`${id}-cookingtime-value`}
									type="number"
									label={__(
										"Cook Time Value",
										"delicious-recipes"
									)}
									value={get(details, [1, "value"])}
									onChange={(newValue) =>
										this.onChangeDetail(
											newValue,
											1,
											"value"
										)
									}
								/>
								<span>{get(details, [1, "unit"])}</span>
							</Fragment>
						)}
					</PanelRow>
					<ToggleControl
						label={__("Display Resting Time", "delicious-recipes")}
						checked={displayRestTime}
						onChange={(display) =>
							this.onChangeSettings(display, "displayRestTime")
						}
					/>
					<PanelRow>
						{displayRestTime && (
							<Fragment>
								<TextControl
									id={`${id}-resttime-label`}
									instanceid={`${id}-resttime-label`}
									type="text"
									label={__(
										"Rest Time Label",
										"delicious-recipes"
									)}
									placeholder={__(
										"Resting Time",
										"delicious-recipes"
									)}
									value={get(details, [2, "label"])}
									onChange={(newValue) =>
										this.onChangeDetail(
											newValue,
											2,
											"label"
										)
									}
								/>
								<TextControl
									id={`${id}-resttime-value`}
									instanceid={`${id}-resttime-value`}
									type="number"
									label={__(
										"Rest Time Value",
										"delicious-recipes"
									)}
									value={get(details, [2, "value"])}
									onChange={(newValue) =>
										this.onChangeDetail(
											newValue,
											2,
											"value"
										)
									}
								/>
								<span>{get(details, [2, "unit"])}</span>
							</Fragment>
						)}
					</PanelRow>
					<ToggleControl
						label={__("Display Total Time", "delicious-recipes")}
						checked={displayTotalTime}
						onChange={(display) =>
							this.onChangeSettings(display, "displayTotalTime")
						}
					/>
					<PanelRow>
						{displayTotalTime && (
							<Fragment>
								<TextControl
									id={`${id}-totaltime-label`}
									instanceid={`${id}-totaltime-label`}
									type="text"
									label={__(
										"Total Time Label",
										"delicious-recipes"
									)}
									placeholder={__(
										"Total Time",
										"delicious-recipes"
									)}
									value={get(details, [3, "label"])}
									onChange={(newValue) =>
										this.onChangeDetail(
											newValue,
											3,
											"label"
										)
									}
								/>
								<TextControl
									id={`${id}-totaltime-value`}
									instanceid={`${id}-totaltime-value`}
									type="number"
									label={__(
										"Total Time Value",
										"delicious-recipes"
									)}
									value={get(details, [3, "value"])}
									onChange={(newValue) =>
										this.onChangeDetail(
											newValue,
											3,
											"value"
										)
									}
								/>
								<span>{get(details, [3, "unit"])}</span>
								<Button
									isSecondary
									className="editor-calculate-total-time"
									onClick={() =>
										this.setState({
											isCalculatedTotalTime: false,
											isCalculateBtnClick: true,
										})
									}
								>
									{__(
										"Calculate Total Time",
										"delicious-recipes"
									)}
								</Button>
								<p className="description">
									{__(
										"Default value: prepTime + cookTime + restTime",
										"delicious-recipes"
									)}
								</p>
							</Fragment>
						)}
					</PanelRow>
					<ToggleControl
						label={__("Display Servings", "delicious-recipes")}
						checked={displayServings}
						onChange={(display) =>
							this.onChangeSettings(display, "displayServings")
						}
					/>
					<PanelRow>
						{displayServings && (
							<Fragment>
								<TextControl
									id={`${id}-yield-label`}
									instanceid={`${id}-yield-label`}
									type="text"
									label={__(
										"Servings Label",
										"delicious-recipes"
									)}
									placeholder={__(
										"Servings",
										"delicious-recipes"
									)}
									value={get(details, [4, "label"])}
									onChange={(newValue) =>
										this.onChangeDetail(
											newValue,
											4,
											"label"
										)
									}
								/>
								<TextControl
									id={`${id}-yield-value`}
									instanceid={`${id}-yield-value`}
									type="number"
									label={__(
										"Servings Value",
										"delicious-recipes"
									)}
									value={get(details, [4, "value"])}
									onChange={(newValue) =>
										this.onChangeDetail(
											newValue,
											4,
											"value"
										)
									}
								/>
								<TextControl
									id={`${id}-yield-unit`}
									instanceid={`${id}-yield-unit`}
									type="text"
									label={__(
										"Servings Unit",
										"delicious-recipes"
									)}
									value={get(details, [4, "unit"])}
									onChange={(newValue) =>
										this.onChangeDetail(newValue, 4, "unit")
									}
								/>
							</Fragment>
						)}
					</PanelRow>
					<ToggleControl
						label={__("Display Calories", "delicious-recipes")}
						checked={displayCalories}
						onChange={(display) =>
							this.onChangeSettings(display, "displayCalories")
						}
					/>
					<PanelRow>
						{displayCalories && (
							<Fragment>
								<TextControl
									id={`${id}-calories-label`}
									instanceid={`${id}-calories-label`}
									type="text"
									label={__(
										"Calories Label",
										"delicious-recipes"
									)}
									placeholder={__(
										"Calories",
										"delicious-recipes"
									)}
									value={get(details, [5, "label"])}
									onChange={(newValue) =>
										this.onChangeDetail(
											newValue,
											5,
											"label"
										)
									}
								/>
								<TextControl
									id={`${id}-calories-value`}
									instanceid={`${id}-calories-value`}
									type="number"
									label={__(
										"Calories Value",
										"delicious-recipes"
									)}
									value={get(details, [5, "value"])}
									onChange={(newValue) =>
										this.onChangeDetail(
											newValue,
											5,
											"value"
										)
									}
								/>
								<span>{get(details, [5, "unit"])}</span>
							</Fragment>
						)}
					</PanelRow>
					<ToggleControl
						label={__("Display Best Season", "delicious-recipes")}
						checked={displayBestSeason}
						onChange={(display) =>
							this.onChangeSettings(display, "displayBestSeason")
						}
					/>
					<PanelRow>
						{displayBestSeason && (
							<Fragment>
								<TextControl
									id={`${id}-season-label`}
									instanceid={`${id}-season-label`}
									type="text"
									label={__(
										"Best Season Label",
										"delicious-recipes"
									)}
									placeholder={__(
										"Best Season",
										"delicious-recipes"
									)}
									value={seasonTitle}
									onChange={(value) =>
										setAttributes({ seasonTitle: value })
									}
								/>
								<SelectControl
									label={__("Best Season")}
									value={season}
									options={[
										{ label: __("Fall", "delicious-recipes"), value: "fall" },
										{ label: __("Winter", "delicious-recipes"), value: "winter" },
										{ label: __("Summer", "delicious-recipes"), value: "summer" },
										{ label: __("Spring", "delicious-recipes"), value: "spring" },
										{
											label:
												__("Suitable throughout the year"),
											value: "available",
										},
									]}
									onChange={(value) =>
										setAttributes({ season: value })
									}
								/>
							</Fragment>
						)}
					</PanelRow>
				</PanelBody>
			</InspectorControls>
		);
	}
}
