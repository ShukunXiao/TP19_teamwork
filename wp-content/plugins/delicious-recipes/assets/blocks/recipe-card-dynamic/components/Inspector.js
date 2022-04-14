/* External dependencies */
import { __ } from "@wordpress/i18n";
import get from "lodash/get";
import map from "lodash/map";
import compact from "lodash/compact";
import isEmpty from "lodash/isEmpty";
import isNull from "lodash/isNull";
import toString from "lodash/toString";
import uniqueId from "lodash/uniqueId";
import isUndefined from "lodash/isUndefined";

/* Internal dependencies */
import VideoUpload from "./VideoUpload";
import { stripHTML } from "../../../helpers/stringHelpers";
import {
	getNumberFromString,
	convertMinutesToHours,
} from "../../../helpers/convertMinutesToHours";
import { pickRelevantMediaFiles } from "../../../helpers/pickRelevantMediaFiles";

/* WordPress dependencies */
const { Component, renderToString, Fragment } = wp.element;
const { RichText, InspectorControls, MediaUpload } = wp.blockEditor;
const {
	BaseControl,
	PanelBody,
	PanelRow,
	ToggleControl,
	TextControl,
	Button,
	FormTokenField,
	SelectControl,
	Notice,
	Icon,
} = wp.components;

/**
 * Module Constants
 */
const ALLOWED_MEDIA_TYPES = ["image"];
const NOT_ADDED = __("Not added", "delicious-recipes");
const NOT_DISPLAYED = (
	<Icon icon="hidden" title={__("Not displayed", "delicious-recipes")} />
);

const coursesToken = [
	__("Appetizers", "delicious-recipes"),
	__("Snacks", "delicious-recipes"),
	__("Breakfast", "delicious-recipes"),
	__("Brunch", "delicious-recipes"),
	__("Dessert", "delicious-recipes"),
	__("Drinks", "delicious-recipes"),
	__("Dinner", "delicious-recipes"),
	__("Main", "delicious-recipes"),
	__("Lunch", "delicious-recipes"),
	__("Salads", "delicious-recipes"),
	__("Sides", "delicious-recipes"),
	__("Soups", "delicious-recipes"),
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

const methodsToken = [
	__("Sautéing", "delicious-recipes"),
	__("Stir-frying", "delicious-recipes"),
	__("Searing", "delicious-recipes"),
	__("Braising", "delicious-recipes"),
	__("Stewing", "delicious-recipes"),
	__("Steaming", "delicious-recipes"),
	__("Baking", "delicious-recipes"),
	__("Roasting", "delicious-recipes"),
	__("Broiling", "delicious-recipes"),
	__("Grilling", "delicious-recipes"),
];

const recipeKeyToken = [
	__("Gluten Free", "delicious-recipes"),
	__("Dairy Free", "delicious-recipes"),
	__("Low Carb", "delicious-recipes"),
	__("Vegetarian", "delicious-recipes"),
	__("Organic", "delicious-recipes"),
	__("Nut Free", "delicious-recipes"),
	__("Egg Free", "delicious-recipes"),
	__("High Protein", "delicious-recipes"),
	__("Keto", "delicious-recipes"),
	__("Vegan", "delicious-recipes"),
];

const keywordsToken = [];

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

		this.onSelectImage = this.onSelectImage.bind(this);
		this.onRemoveRecipeImage = this.onRemoveRecipeImage.bind(this);
		this.onChangeDetail = this.onChangeDetail.bind(this);
		this.onChangeSettings = this.onChangeSettings.bind(this);
		this.onUpdateURL = this.onUpdateURL.bind(this);

		this.state = {
			updateIngredients: false,
			updateInstructions: false,
			isCalculatedTotalTime: false,
			isCalculateBtnClick: false,
			structuredDataNotice: {
				errors: [],
				warnings: [],
				not_display: [],
			},
			structuredDataTable: {
				recipeIngredients: 0,
				recipeInstructions: 0,
			},
		};
	}

	componentDidMount() {
		this.setFeaturedImage();
		this.structuredDataTable();
		this.calculateTotalTime();
	}

	componentDidUpdate(prevProps) {
		const { attributes } = this.props;
		const prevAttributes = prevProps.attributes;

		if (!attributes.hasImage && this.props.media !== prevProps.media) {
			this.setFeaturedImage();
		}

		if (
			attributes.ingredients !== prevAttributes.ingredients ||
			attributes.steps !== prevAttributes.steps
		) {
			this.structuredDataTable();
		}

		if (!this.state.isCalculatedTotalTime) {
			this.calculateTotalTime();
		}
	}

	/*
	 * Set featured image if Recipe Card image aren't uploaded
	 */
	setFeaturedImage() {
		const {
			media,
			attributes: { hasImage },
			setAttributes,
		} = this.props;

		if (hasImage || !media) {
			return;
		}

		const relevantMedia = pickRelevantMediaFiles(media, "header");

		setAttributes({
			hasImage: !isNull(relevantMedia.id),
			image: {
				id: relevantMedia.id,
				url: relevantMedia.url,
				alt: relevantMedia.alt,
				title: relevantMedia.title,
				sizes:
					get(media, ["sizes"]) ||
					get(media, ["media_details", "sizes"]),
			},
		});
	}

	onSelectImage(media) {
		const { setAttributes } = this.props;
		const relevantMedia = pickRelevantMediaFiles(media, "header");

		setAttributes({
			hasImage: !isNull(relevantMedia.id),
			image: {
				id: relevantMedia.id,
				url: relevantMedia.url,
				alt: relevantMedia.alt,
				title: relevantMedia.title,
				sizes: media.sizes,
			},
		});
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

	onRemoveRecipeImage() {
		const { setAttributes } = this.props;

		setAttributes({ hasImage: false, image: null });
	}

	onUpdateURL(url) {
		const {
			setAttributes,
			attributes: {
				image: { id, alt, sizes },
			},
		} = this.props;

		setAttributes({
			hasImage: true,
			image: {
				id: id,
				url: url,
				alt: alt,
				sizes: sizes,
			},
		});
	}

	errorDetails() {
		const string = toString(this.state.structuredDataNotice.errors);
		return string.replace(/,/g, ", ");
	}

	warningDetails() {
		const string = toString(this.state.structuredDataNotice.warnings);
		return string.replace(/,/g, ", ");
	}

	notDisplayDetails() {
		const string = toString(this.state.structuredDataNotice.not_display);
		return string.replace(/,/g, ", ");
	}

	structuredDataTable() {
		const { ingredients, steps } = this.props.attributes;

		let recipeIngredients = 0;
		let recipeInstructions = 0;

		ingredients.forEach((ingredient) => {
			const jsonName = get(ingredient, "jsonName");

			if (!isEmpty(jsonName)) {
				recipeIngredients++;
			}
		});

		steps.forEach((step) => {
			const jsonText = get(step, "jsonText");

			if (!isEmpty(jsonText)) {
				recipeInstructions++;
			}
		});

		this.setState(
			{ structuredDataTable: { recipeIngredients, recipeInstructions } },
			this.structuredDataNotice
		);
	}

	structuredDataNotice() {
		const { structuredDataTable } = this.state;
		const {
			hasImage,
			details,
			course,
			cuisine,
			method,
			recipeKey,
			keywords,
			summary,
			hasVideo,
			settings: {
				0: {
					displayPrepTime,
					displayCookingTime,
					displayRestTime,
					displayCourse,
					displayCuisine,
					displayCookingMethod,
					displayRecipeKey,
					displayCalories,
				},
			},
		} = this.props.attributes;

		const not_display = [];
		const warnings = [];
		const errors = [];

		// Push warnings
		RichText.isEmpty(summary) && warnings.push("summary");
		!hasVideo && warnings.push("video");
		!get(details, [0, "value"]) && warnings.push("prepTime");
		!get(details, [1, "value"]) && warnings.push("cookTime");
		!get(details, [5, "value"]) && warnings.push("calories");
		isEmpty(course) && warnings.push("course");
		isEmpty(cuisine) && warnings.push("cuisine");
		isEmpty(keywords) && warnings.push("keywords");

		// Push not displayed
		!displayCookingTime && not_display.push("cookTime");
		!displayPrepTime && not_display.push("prepTime");
		!displayCalories && not_display.push("calories");
		!displayCuisine && not_display.push("cuisine");
		!displayCourse && not_display.push("course");

		// Push errors
		!hasImage && errors.push("image");
		!get(structuredDataTable, "recipeIngredients") &&
			errors.push("ingredients");
		!get(structuredDataTable, "recipeInstructions") && errors.push("steps");

		this.setState({
			structuredDataNotice: { warnings, errors, not_display },
		});
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
	 * @returns {Component} The Ingredient items block settings.
	 */
	render() {
		const { className, attributes, setAttributes } = this.props;

		const { structuredDataNotice, structuredDataTable } = this.state;

		const {
			id,
			hasImage,
			image,
			hasVideo,
			video,
			recipeTitle,
			summary,
			jsonSummary,
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
					print_btn,
					pin_btn,
					custom_author_name,
					displayCourse,
					displayCuisine,
					displayCookingMethod,
					displayRecipeKey,
					displayDifficulty,
					displayAuthor,
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

		return (
			<InspectorControls>
				<PanelBody
					className="delicious-recipes-settings"
					initialOpen={true}
					title={__("Recipe Card Settings", "delicious-recipes")}
				>
					<BaseControl
						id={`${id}-image`}
						className="editor-post-featured-image"
						label={__(
							"Recipe Card Image (required)",
							"delicious-recipes"
						)}
						help={__(
							"Upload image for Recipe Card.",
							"delicious-recipes"
						)}
					>
						{!hasImage && (
							<MediaUpload
								onSelect={this.onSelectImage}
								allowedTypes={ALLOWED_MEDIA_TYPES}
								value={get(image, ["id"])}
								render={({ open }) => (
									<Button
										className="editor-post-featured-image__toggle"
										onClick={open}
									>
										{__(
											"Add Recipe Image",
											"delicious-recipes"
										)}
									</Button>
								)}
							/>
						)}
						{hasImage && (
							<Fragment>
								<MediaUpload
									onSelect={this.onSelectImage}
									allowedTypes={ALLOWED_MEDIA_TYPES}
									value={get(image, ["id"])}
									render={({ open }) => (
										<Button
											className="editor-post-featured-image__preview"
											onClick={open}
										>
											<img
												className={`${id}-image`}
												src={
													get(image, [
														"sizes",
														"full",
														"url",
													]) ||
													get(image, [
														"sizes",
														"full",
														"source_url",
													]) ||
													get(image, ["url"]) ||
													get(image, ["source_url"])
												}
												alt={
													get(image, ["alt"]) ||
													recipeTitle
												}
											/>
										</Button>
									)}
								/>
								<MediaUpload
									onSelect={this.onSelectImage}
									allowedTypes={ALLOWED_MEDIA_TYPES}
									value={get(image, ["id"])}
									render={({ open }) => (
										<Button isSecondary onClick={open}>
											{__(
												"Replace Image",
												"delicious-recipes"
											)}
										</Button>
									)}
								/>
								<Button
									isLink="true"
									isDestructive="true"
									onClick={this.onRemoveRecipeImage}
								>
									{__(
										"Remove Recipe Image",
										"delicious-recipes"
									)}
								</Button>
							</Fragment>
						)}
					</BaseControl>
					<Fragment>
						<BaseControl
							id={`${id}-print-btn`}
							label={__("Print Button", "delicious-recipes")}
						>
							<ToggleControl
								label={__(
									"Display Print Button",
									"delicious-recipes"
								)}
								checked={print_btn}
								onChange={(display) =>
									this.onChangeSettings(display, "print_btn")
								}
							/>
						</BaseControl>
						<BaseControl
							id={`${id}-pinit-btn`}
							label={__("Pinterest Button", "delicious-recipes")}
						>
							<ToggleControl
								label={__(
									"Display Pinterest Button",
									"delicious-recipes"
								)}
								checked={pin_btn}
								onChange={(display) =>
									this.onChangeSettings(display, "pin_btn")
								}
							/>
						</BaseControl>
					</Fragment>
					<BaseControl
						id={`${id}-author`}
						label={__("Author", "delicious-recipes")}
					>
						<ToggleControl
							label={__("Display Author", "delicious-recipes")}
							checked={displayAuthor}
							onChange={(display) =>
								this.onChangeSettings(display, "displayAuthor")
							}
						/>
						{displayAuthor && (
							<TextControl
								id={`${id}-custom-author-name`}
								instanceid={`${id}-custom-author-name`}
								type="text"
								label={__(
									"Custom author name",
									"delicious-recipes"
								)}
								help={__(
									"Default: Post author name",
									"delicious-recipes"
								)}
								value={custom_author_name}
								onChange={(authorName) =>
									this.onChangeSettings(
										authorName,
										"custom_author_name"
									)
								}
							/>
						)}
					</BaseControl>
				</PanelBody>
				<VideoUpload {...{ attributes, setAttributes, className }} />
				<PanelBody
					className="delicious-recipes-seo-settings"
					initialOpen={true}
					title={__("Recipe Card SEO Settings", "delicious-recipes")}
				>
					<BaseControl
						id={`${id}-course`}
						label={__("Course (required)", "delicious-recipes")}
						help={__(
							"The post category is added by default.",
							"delicious-recipes"
						)}
					>
						<ToggleControl
							label={__("Display Course", "delicious-recipes")}
							checked={displayCourse}
							onChange={(display) =>
								this.onChangeSettings(display, "displayCourse")
							}
						/>
						{displayCourse && (
							<FormTokenField
								label={__("Add course", "delicious-recipes")}
								value={course}
								suggestions={coursesToken}
								onChange={(newCourse) =>
									setAttributes({ course: newCourse })
								}
								placeholder={__(
									"Type course and press Enter",
									"delicious-recipes"
								)}
							/>
						)}
					</BaseControl>
					<BaseControl
						id={`${id}-cuisine`}
						label={__("Cuisine (required)", "delicious-recipes")}
					>
						<ToggleControl
							label={__("Display Cuisine", "delicious-recipes")}
							checked={displayCuisine}
							onChange={(display) =>
								this.onChangeSettings(display, "displayCuisine")
							}
						/>
						{displayCuisine && (
							<FormTokenField
								label={__("Add cuisine", "delicious-recipes")}
								value={cuisine}
								suggestions={cuisinesToken}
								onChange={(newCuisine) =>
									setAttributes({ cuisine: newCuisine })
								}
								placeholder={__(
									"Type cuisine and press Enter",
									"delicious-recipes"
								)}
							/>
						)}
					</BaseControl>
					<BaseControl
						id={`${id}-method`}
						label={__(
							"Cooking Method (required)",
							"delicious-recipes"
						)}
					>
						<ToggleControl
							label={__(
								"Display Cooking Method",
								"delicious-recipes"
							)}
							checked={displayCookingMethod}
							onChange={(display) =>
								this.onChangeSettings(
									display,
									"displayCookingMethod"
								)
							}
						/>
						{displayCookingMethod && (
							<FormTokenField
								label={__("Add method", "delicious-recipes")}
								value={method}
								suggestions={methodsToken}
								onChange={(newCookingMethod) =>
									setAttributes({ method: newCookingMethod })
								}
								placeholder={__(
									"Type cooking method and press Enter",
									"delicious-recipes"
								)}
							/>
						)}
					</BaseControl>
					<BaseControl
						id={`${id}-recipeKey`}
						label={__("Recipe Key (required)", "delicious-recipes")}
					>
						<ToggleControl
							label={__(
								"Display Recipe Key",
								"delicious-recipes"
							)}
							checked={displayRecipeKey}
							onChange={(display) =>
								this.onChangeSettings(
									display,
									"displayRecipeKey"
								)
							}
						/>
						{displayRecipeKey && (
							<FormTokenField
								label={__(
									"Add recipe keys",
									"delicious-recipes"
								)}
								value={recipeKey}
								suggestions={recipeKeyToken}
								onChange={(newRecipeKey) =>
									setAttributes({ recipeKey: newRecipeKey })
								}
								placeholder={__(
									"Type recipe keys and press Enter",
									"delicious-recipes"
								)}
							/>
						)}
					</BaseControl>
					<BaseControl
						id={`${id}-keywords`}
						label={__(
							"Keywords (recommended)",
							"delicious-recipes"
						)}
						help={__(
							"For multiple keywords add `,` after each keyword (ex: keyword, keyword, keyword). Note: The post tags is added by default.",
							"delicious-recipes"
						)}
					>
						<FormTokenField
							label={__("Add keywords", "delicious-recipes")}
							value={keywords}
							suggestions={keywordsToken}
							onChange={(newKeyword) =>
								setAttributes({ keywords: newKeyword })
							}
							placeholder={__(
								"Type recipe keywords",
								"delicious-recipes"
							)}
						/>
					</BaseControl>
				</PanelBody>
				<PanelBody
					className="delicious-recipes-details"
					initialOpen={true}
					title={__("Recipe Card Details", "delicious-recipes")}
				>
					{!get(attributes, ["settings", 1, "isNoticeDismiss"]) && (
						<Notice
							status="info"
							onRemove={() =>
								this.onChangeSettings(
									true,
									"isNoticeDismiss",
									1
								)
							}
						>
							<p>
								{__(
									"The following details are used for Schema Markup (Rich Snippets). If you want to hide some details in the post, just turn them off below.",
									"delicious-recipes"
								)}
							</p>
						</Notice>
					)}
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
												__("Suitable throughout the year", "delicious-recipes"),
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
				<PanelBody
					className="delicious-recipes-structured-data-testing"
					initialOpen={true}
					title={__("Structured Data Testing", "delicious-recipes")}
				>
					<BaseControl
						id={`${id}-counters`}
						help={__(
							"Automatically check Structured Data errors and warnings.",
							"delicious-recipes"
						)}
					>
						{get(structuredDataNotice, "errors").length > 0 && (
							<Notice status="error" isDismissible={false}>
								<p>
									{__(
										"Please enter value for required fields: ",
										"delicious-recipes"
									)}{" "}
									<strong>{this.errorDetails()}</strong>.
								</p>
							</Notice>
						)}
						{get(structuredDataNotice, "warnings").length > 0 && (
							<Notice status="warning" isDismissible={false}>
								<p>
									{__(
										"We recommend to add value for following fields: ",
										"delicious-recipes"
									)}{" "}
									<strong>{this.warningDetails()}</strong>.
								</p>
							</Notice>
						)}
						{get(structuredDataNotice, "not_display").length >
							0 && (
							<Notice status="warning" isDismissible={false}>
								<p>
									{__(
										"We recommend to display following fields: ",
										"delicious-recipes"
									)}{" "}
									<strong>{this.notDisplayDetails()}</strong>.
								</p>
							</Notice>
						)}
						<PanelRow
							className={
								recipeTitle
									? "text-color-green"
									: "text-color-red"
							}
						>
							<span>recipeTitle</span>
							<strong>{recipeTitle}</strong>
						</PanelRow>
						<PanelRow
							className={
								RichText.isEmpty(summary)
									? "text-color-orange"
									: "text-color-green"
							}
						>
							<span>description</span>
							<strong>
								{!isUndefined(jsonSummary)
									? stripHTML(jsonSummary)
									: NOT_ADDED}
							</strong>
						</PanelRow>
						<PanelRow
							className={
								!hasImage
									? "text-color-red"
									: "text-color-green"
							}
						>
							<span>image</span>
							<strong>
								{hasImage ? get(image, "url") : NOT_ADDED}
							</strong>
						</PanelRow>
						<PanelRow
							className={
								!hasVideo
									? "text-color-orange"
									: "text-color-green"
							}
						>
							<span>video</span>
							<strong>
								{hasVideo ? get(video, "url") : NOT_ADDED}
							</strong>
						</PanelRow>
						<PanelRow
							className={
								isEmpty(keywords)
									? "text-color-orange"
									: "text-color-green"
							}
						>
							<span>keywords</span>
							<strong>
								{!isEmpty(keywords)
									? keywords.filter((item) => item).join(", ")
									: NOT_ADDED}
							</strong>
						</PanelRow>
						<PanelRow
							className={
								!displayCourse || isEmpty(course)
									? "text-color-orange"
									: "text-color-green"
							}
						>
							<span>recipeCategory</span>
							{displayCourse && (
								<strong>
									{!isEmpty(course)
										? course
												.filter((item) => item)
												.join(", ")
										: NOT_ADDED}
								</strong>
							)}
							{!displayCourse && <strong>{NOT_DISPLAYED}</strong>}
						</PanelRow>
						<PanelRow
							className={
								!displayCuisine || isEmpty(cuisine)
									? "text-color-orange"
									: "text-color-green"
							}
						>
							<span>recipeCuisine</span>
							{displayCuisine && (
								<strong>
									{!isEmpty(cuisine)
										? cuisine
												.filter((item) => item)
												.join(", ")
										: NOT_ADDED}
								</strong>
							)}
							{!displayCuisine && (
								<strong>{NOT_DISPLAYED}</strong>
							)}
						</PanelRow>
						<PanelRow
							className={
								!displayCookingMethod || isEmpty(method)
									? "text-color-orange"
									: "text-color-green"
							}
						>
							<span>cookingMethod</span>
							{displayCookingMethod && (
								<strong>
									{!isEmpty(method)
										? method
												.filter((item) => item)
												.join(", ")
										: NOT_ADDED}
								</strong>
							)}
							{!displayCookingMethod && (
								<strong>{NOT_DISPLAYED}</strong>
							)}
						</PanelRow>
						<PanelRow
							className={
								displayServings &&
								get(details, [4, "value"]) &&
								"text-color-green"
							}
						>
							<span>recipeYield</span>
							{displayServings && (
								<strong>
									{get(details, [4, "value"])
										? get(details, [4, "value"]) +
										  " " +
										  get(details, [4, "unit"])
										: NOT_ADDED}
								</strong>
							)}
							{!displayServings && (
								<strong>{NOT_DISPLAYED}</strong>
							)}
						</PanelRow>
						<PanelRow
							className={
								!displayPrepTime || !get(details, [0, "value"])
									? "text-color-orange"
									: "text-color-green"
							}
						>
							<span>prepTime</span>
							{displayPrepTime && (
								<strong>
									{get(details, [0, "value"])
										? convertMinutesToHours(
												get(details, [0, "value"])
										  )
										: NOT_ADDED}
								</strong>
							)}
							{!displayPrepTime && (
								<strong>{NOT_DISPLAYED}</strong>
							)}
						</PanelRow>
						<PanelRow
							className={
								!displayCookingTime ||
								!get(details, [1, "value"])
									? "text-color-orange"
									: "text-color-green"
							}
						>
							<span>cookTime</span>
							{displayCookingTime && (
								<strong>
									{get(details, [1, "value"])
										? convertMinutesToHours(
												get(details, [1, "value"])
										  )
										: NOT_ADDED}
								</strong>
							)}
							{!displayCookingTime && (
								<strong>{NOT_DISPLAYED}</strong>
							)}
						</PanelRow>
						<PanelRow
							className={
								displayTotalTime &&
								get(details, [3, "value"]) &&
								"text-color-green"
							}
						>
							<span>totalTime</span>
							{displayTotalTime && (
								<strong>
									{get(details, [3, "value"])
										? convertMinutesToHours(
												get(details, [3, "value"])
										  )
										: NOT_ADDED}
								</strong>
							)}
							{!displayTotalTime && (
								<strong>{NOT_DISPLAYED}</strong>
							)}
						</PanelRow>
						<PanelRow
							className={
								!displayCalories || !get(details, [5, "value"])
									? "text-color-orange"
									: "text-color-green"
							}
						>
							<span>calories</span>
							{displayCalories && (
								<strong>
									{get(details, [5, "value"])
										? get(details, [5, "value"]) +
										  " " +
										  get(details, [5, "unit"])
										: NOT_ADDED}
								</strong>
							)}
							{!displayCalories && (
								<strong>{NOT_DISPLAYED}</strong>
							)}
						</PanelRow>
						<PanelRow
							className={
								!get(structuredDataTable, "recipeIngredients")
									? "text-color-red"
									: "text-color-green"
							}
						>
							<span>
								{__("Ingredients", "delicious-recipes")}
							</span>
							<strong>
								{get(structuredDataTable, "recipeIngredients")
									? get(
											structuredDataTable,
											"recipeIngredients"
									  )
									: NOT_ADDED}
							</strong>
						</PanelRow>
						<PanelRow
							className={
								!get(structuredDataTable, "recipeInstructions")
									? "text-color-red"
									: "text-color-green"
							}
						>
							<span>{__("Steps", "delicious-recipes")}</span>
							<strong>
								{get(structuredDataTable, "recipeInstructions")
									? get(
											structuredDataTable,
											"recipeInstructions"
									  )
									: NOT_ADDED}
							</strong>
						</PanelRow>
					</BaseControl>
				</PanelBody>
			</InspectorControls>
		);
	}
}
