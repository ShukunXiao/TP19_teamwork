/* External dependencies */
import { __ } from "@wordpress/i18n";
import get from "lodash/get";
import ceil from "lodash/ceil";
import filter from "lodash/filter";
import findKey from "lodash/findKey";

/* Internal dependencies */
import { parseClassName } from "../../helpers/getBlockStyle";

/* WordPress dependencies */
const { Component, Fragment } = wp.element;
const { withSelect } = wp.data;
const { compose } = wp.compose;
const { TextControl, Button } = wp.components;

const labels = delrcp.nutritionFactsLabel;

class Nutrition extends Component {
	constructor(props) {
		super(props);

		this.preFillData = this.preFillData.bind(this);
		this.onChangeData = this.onChangeData.bind(this);

		this.state = {
			isDataPreFill: false,
			reloadValues: false,
		};
	}

	preFillData() {
		const {
			setAttributes,
			attributes: { data },
			blockData: { details },
		} = this.props;

		if (!details) {
			return;
		}

		const newData = data || {};

		const servings = get(details, [4, "value"]);
		const calories = get(details, [5, "value"]);

		if (this.state.reloadValues) {
			newData.servings = servings ? servings : get(data, "servings");
			newData.calories = calories ? calories : get(data, "calories");
		}

		if (!get(data, "servings")) {
			newData.servings = servings;
		}
		if (!get(data, "calories")) {
			newData.calories = calories;
		}

		setAttributes({ data: { ...newData } });

		this.setState({ isDataPreFill: true });
	}

	onChangeData(newValue, index) {
		const {
			setAttributes,
			attributes: { data },
		} = this.props;

		const newData = data || {};

		newData[index] = newValue;

		setAttributes({ data: { ...newData } });
	}

	onChangeSettings(newValue, index) {
		const {
			setAttributes,
			attributes: { settings },
		} = this.props;

		const newData = settings || {};

		newData[index] = newValue;

		setAttributes({ settings: { ...newData } });
	}

	getValue(label_id) {
		const { data } = this.props.attributes;
		return get(data, label_id);
	}

	getLabelTitle(label_id) {
		const key = findKey(labels, function (o) {
			return o.id === label_id;
		});
		return get(labels, [key, "label"]);
	}

	getPDV(label_id) {
		const key = findKey(labels, function (o) {
			return o.id === label_id;
		});
		return get(labels, [key, "pdv"]);
	}

	drawNutritionLabels() {
		const { id, data } = this.props.attributes;

		return labels.map((label, index) => {
			return (
				<div key={index} className="dr-field dr-text">
					<label className="dr-field-label">{label.label}</label>
					<div className="dr-floated">
						<TextControl
							id={`${id}-${label.id}`}
							instanceid={`${id}-${label.id}`}
							type={label.type || 'number'}
							value={get(data, label.id)}
							onChange={(newValue) =>
								this.onChangeData(newValue, label.id)
							}
						/>
					</div>
				</div>
			);
		});
	}

	drawNutrientsList() {
		const { data } = this.props.attributes;

		return labels.map((label, index) => {
			const value = get(data, label.id);

			if (index <= 13) {
				return;
			}

			if (!value) {
				return;
			}

			return (
				<dt key={index}>
					<strong>
						{label.label}{" "}
						<span className="dr-nut-percent dr-nut-label">
							{value}
						</span>
						{__("%", "delicious-recipes")}
					</strong>
				</dt>
			);
		});
	}

	drawNutritionFacts() {
		return (
			<div className="dr-nutrition-facts">
				<div
					className="dr-title-wrap"
					style={{
						backgroundColor: delrcp.setting_options.primaryColor,
					}}
				>
					<div className="dr-title">
						<b>{__("Nutrition Facts", "delicious-recipes")}</b>
					</div>
				</div>
				<div className="dr-nutrition-list">
					<div className="dr-nutrition-label">
						<p>
							{this.getValue("servingSize") && (
								<Fragment>
									{this.getLabelTitle("servingSize")}{" "}
									<strong className="dr-nut-label">
										{this.getValue("servingSize")}
									</strong>
								</Fragment>
							)}
						</p>
						<p>
							{this.getValue("servings") && (
								<Fragment>
									{this.getLabelTitle("servings")}{" "}
									<strong className="dr-nut-label">
										{this.getValue("servings")}
									</strong>
								</Fragment>
							)}
						</p>
                        <hr className="dr-nut-hr" 
                            style={{
                                borderTop: `1rem solid ${delrcp.setting_options.primaryColor}`,
                            }}
                        />
						<dl>
							<dt>
								<strong className="dr-nut-heading">
									{__(
										"Amount Per Serving",
										"delicious-recipes"
									)}
								</strong>
							</dt>
							<section className="dr-clearfix">
								{this.getValue("calories") && (
									<dt>
										<strong>
											{this.getLabelTitle("calories")}
										</strong>{" "}
										<strong className="dr-nut-label">
											{this.getValue("calories")}
										</strong>
									</dt>
								)}
								{this.getValue("caloriesFromFat") && (
									<dt>
										<strong>
											{this.getLabelTitle(
												"caloriesFromFat"
											)}
										</strong>{" "}
										<strong className="dr-nut-label">
											{this.getValue("caloriesFromFat")}
										</strong>
									</dt>
								)}
							</section>
                            <dt className="dr-nut-spacer" 
                                style={{
                                    backgroundColor: delrcp.setting_options.primaryColor,
                                }}
                            >
                            </dt>
							<dt className="dr-nut-no-border">
								<strong className="dr-nut-heading dr-nut-right">
									{__("% Daily Value *", "delicious-recipes")}
								</strong>
							</dt>
							<section className="dr-clearfix">
								{this.getValue("totalFat") && (
									<dt>
										<strong>
											{this.getLabelTitle("totalFat")}
										</strong>{" "}
										<strong className="dr-nut-label">
											{this.getValue("totalFat")}
										</strong>
										<strong className="dr-nut-label dr-nut-measurement">
											{__("g", "delicious-recipes")}
										</strong>
										<strong className="dr-nut-right">
											<span className="dr-nut-percent">
												{ceil(
													(this.getValue("totalFat") /
														this.getPDV(
															"totalFat"
														)) *
														100
												)}
											</span>
											%
										</strong>
										<dl>
											<dt>
												<strong>
													{this.getLabelTitle(
														"saturatedFat"
													)}
												</strong>{" "}
												<strong className="dr-nut-label">
													{this.getValue(
														"saturatedFat"
													)}
												</strong>
												<strong className="dr-nut-label dr-nut-measurement">
													{__("g", "delicious-recipes")}
												</strong>
												<strong className="dr-nut-right">
													<span className="dr-nut-percent">
														{ceil(
															(this.getValue(
																"saturatedFat"
															) /
																this.getPDV(
																	"saturatedFat"
																)) *
																100
														)}
													</span>
													%
												</strong>
											</dt>
											<dt>
												<strong>
													{this.getLabelTitle(
														"transFat"
													)}
												</strong>{" "}
												<strong className="dr-nut-label">
													{this.getValue("transFat")}
												</strong>
												<strong className="dr-nut-label dr-nut-measurement">
													{__("g", "delicious-recipes")}
												</strong>
											</dt>
										</dl>
									</dt>
								)}
								{this.getValue("cholesterol") && (
									<dt>
										<strong>
											{this.getLabelTitle("cholesterol")}
										</strong>{" "}
										<strong className="dr-nut-label">
											{this.getValue("cholesterol")}
										</strong>
										<strong className="dr-nut-label dr-nut-measurement">
											{__("mg", "delicious-recipes")}
										</strong>
										<strong className="dr-nut-right">
											<span className="dr-nut-percent">
												{ceil(
													(this.getValue(
														"cholesterol"
													) /
														this.getPDV(
															"cholesterol"
														)) *
														100
												)}
											</span>
											%
										</strong>
									</dt>
								)}
								{this.getValue("sodium") && (
									<dt>
										<strong>
											{this.getLabelTitle("sodium")}
										</strong>{" "}
										<strong className="dr-nut-label">
											{this.getValue("sodium")}
										</strong>
										<strong className="dr-nut-label dr-nut-measurement">
											{__("mg", "delicious-recipes")}
										</strong>
										<strong className="dr-nut-right">
											<span className="dr-nut-percent">
												{ceil(
													(this.getValue("sodium") /
														this.getPDV("sodium")) *
														100
												)}
											</span>
											%
										</strong>
									</dt>
								)}
								{this.getValue("potassium") && (
									<dt>
										<strong>
											{this.getLabelTitle("potassium")}
										</strong>{" "}
										<strong className="dr-nut-label">
											{this.getValue("potassium")}
										</strong>
										<strong className="dr-nut-label dr-nut-measurement">
											{__("mg", "delicious-recipes")}
										</strong>
										<strong className="dr-nut-right">
											<span className="dr-nut-percent">
												{ceil(
													(this.getValue(
														"potassium"
													) /
														this.getPDV(
															"potassium"
														)) *
														100
												)}
											</span>
											%
										</strong>
									</dt>
								)}
								{this.getValue("totalCarbohydrate") && (
									<dt>
										<strong>
											{this.getLabelTitle(
												"totalCarbohydrate"
											)}
										</strong>{" "}
										<strong className="dr-nut-label">
											{this.getValue("totalCarbohydrate")}
										</strong>
										<strong className="dr-nut-label dr-nut-measurement">
											{__("g", "delicious-recipes")}
										</strong>
										<strong className="dr-nut-right">
											<span className="dr-nut-percent">
												{ceil(
													(this.getValue(
														"totalCarbohydrate"
													) /
														this.getPDV(
															"totalCarbohydrate"
														)) *
														100
												)}
											</span>
											%
										</strong>
										<dl>
											<dt>
												<strong>
													{this.getLabelTitle(
														"dietaryFiber"
													)}
												</strong>{" "}
												<strong className="dr-nut-label">
													{this.getValue(
														"dietaryFiber"
													)}
												</strong>
												<strong className="dr-nut-label dr-nut-measurement">
													{__("g", "delicious-recipes")}
												</strong>
												<strong className="dr-nut-right">
													<span className="dr-nut-percent">
														{ceil(
															(this.getValue(
																"dietaryFiber"
															) /
																this.getPDV(
																	"dietaryFiber"
																)) *
																100
														)}
													</span>
													%
												</strong>
											</dt>
											<dt>
												<strong>
													{this.getLabelTitle(
														"sugars"
													)}
												</strong>{" "}
												<strong className="dr-nut-label">
													{this.getValue("sugars")}
												</strong>
												<strong className="dr-nut-label dr-nut-measurement">
													{__("g", "delicious-recipes")}
												</strong>
											</dt>
										</dl>
									</dt>
								)}
								{this.getValue("protein") && (
									<dt>
										<strong>
											{this.getLabelTitle("protein")}
										</strong>{" "}
										<strong className="dr-nut-label">
											{this.getValue("protein")}
										</strong>
										<strong className="dr-nut-label dr-nut-measurement">
											{__("g", "delicious-recipes")}
										</strong>
										<strong className="dr-nut-right">
											<span className="dr-nut-percent">
												{ceil(
													(this.getValue("protein") /
														this.getPDV(
															"protein"
														)) *
														100
												)}
											</span>
											%
										</strong>
									</dt>
								)}
							</section>
						</dl>
						<hr className="dr-nut-hr" 
                            style={{
                                borderTop: `1rem solid ${delrcp.setting_options.primaryColor}`,
                            }}
                        />
						<dl className="dr-nut-bottom dr-clearfix">
							{this.drawNutrientsList()}
						</dl>
						<p className="dr-daily-value-text">
							{__(
								"* Percent Daily Values are based on a 2,000 calorie diet. Your daily value may be higher or lower depending on your calorie needs.",
								"delicious-recipes"
							)}
						</p>
					</div>
				</div>
			</div>
		);
	}

	render() {
		const {
			className,
			attributes: { id, settings },
		} = this.props;

		const blockClassName = parseClassName(className);

		if (!this.state.isDataPreFill) {
			this.preFillData();
		}

		return (
			<div id={id} className="dr-tab-content dr-nutrition-content">
				<div className="dr-form-block">
					<div className="dr-title-wrap"
                        style={{
                            backgroundColor: delrcp.setting_options.primaryColor,
                        }}
                    >
						<h3 className="dr-title">
							{__("Nutrition Information", "delicious-recipes")}
						</h3>
						<div className="dr-description">
							{__(
								"Enter nutrition details values for the recipe. A preview of nutrition chart will be shown according to the values.",
								"delicious-recipes"
							)}
						</div>
					</div>
					<div className="dr-block-content">
						<div className="dr-form-block-wrap dr-floated">
							<div className="dr-form-content dr-floated">
								{this.drawNutritionLabels()}
							</div>
							{this.drawNutritionFacts()}
						</div>
					</div>
					<Button
						className={`${blockClassName}-reload-values`}
						title={__(
							"In case you made some changes to Recipe Card, press button to Reload values.",
							"delicious-recipes"
						)}
						isDefault
						onClick={() =>
							this.setState({
								reloadValues: true,
								isDataPreFill: false,
							})
						}
					>
						{__("Reload Values", "delicious-recipes")}
					</Button>
				</div>
			</div>
		);
	}
}

const applyWithSelect = withSelect((select, props) => {
	const { getBlocks } = select("core/block-editor");

	const blocksList = getBlocks();
	const recipeCardBlock = filter(blocksList, function (item) {
		return "delicious-recipes/dynamic-recipe-card" === item.name;
	});

	return {
		blockData: get(recipeCardBlock, [0, "attributes"]) || {},
	};
});

export default compose(applyWithSelect)(Nutrition);
