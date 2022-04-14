/*global delrcp*/

/* External dependencies */
import apiFetch from "@wordpress/api-fetch";
import {
	AlignmentToolbar,
	BlockControls,
	MediaUpload,
	RichText,
} from "@wordpress/block-editor";
import { Button, Disabled, Placeholder, Spinner } from "@wordpress/components";
import { compose } from "@wordpress/compose";
import { withSelect } from "@wordpress/data";
/* WordPress dependencies */
import { Component, Fragment, renderToString } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { positionCenter, positionLeft, positionRight } from "@wordpress/icons";
import { addQueryArgs } from "@wordpress/url";
import get from "lodash/get";
import invoke from "lodash/invoke";
import isEmpty from "lodash/isEmpty";
import map from "lodash/map";
import ReactPlayer from "react-player";
import { generateId } from "../../../helpers/generateId";
import { pickRelevantMediaFiles } from "../../../helpers/pickRelevantMediaFiles";
import { stripHTML } from "../../../helpers/stringHelpers";
import Detail from "./Detail";
import Direction from "./Direction";
import Ingredient from "./Ingredient";
import Inspector from "./Inspector";
import icons from "../../common/icons";

/**
 * Module Constants
 */
const ALLOWED_MEDIA_TYPES = ["image"];
const DEFAULT_QUERY = {
	per_page: -1,
	orderby: "name",
	order: "asc",
	_fields: "id,name",
};

const { setting_options, pluginURL } = delrcp;

/**
 * A Recipe Card block.
 */
class RecipeCard extends Component {
	/**
	 * Constructs a Recipe Card editor component.
	 *
	 * @param {Object} props This component's properties.
	 *
	 * @returns {void}
	 */
	constructor(props) {
		super(props);

		this.setFocus = this.setFocus.bind(this);
		this.onSelectImage = this.onSelectImage.bind(this);

		this.editorRefs = {};
		this.state = {
			isLoading: true,
			isPostTitleSet: false,
			isCategoriesFetched: false,
			isTagsFetched: false,
			focus: "",
		};
	}

	componentDidMount() {
		this.setPostTitle();
		this.fetchCategories();
		this.fetchTags();
	}

	componentWillUnmount() {
		invoke(this.fetchRequest, ["abort"]);
	}

	componentDidUpdate(prevProps, prevState) {
		if (
			this.state.isPostTitleSet &&
			!prevState.isPostTitleSet &&
			RichText.isEmpty(this.props.attributes.recipeTitle)
		) {
			this.setState({ isLoading: true });
			this.setPostTitle();
		}

		if (
			this.state.isCategoriesFetched &&
			!prevState.isCategoriesFetched &&
			isEmpty(this.props.attributes.course)
		) {
			this.setState({ isLoading: true });
			this.fetchCategories();
		}

		if (
			this.state.isTagsFetched &&
			!prevState.isTagsFetched &&
			isEmpty(this.props.attributes.keywords)
		) {
			this.setState({ isLoading: true });
			this.fetchTags();
		}
	}

	setPostTitle() {
		const { postTitle } = this.props;

		if (!RichText.isEmpty(this.props.attributes.recipeTitle)) {
			return;
		}

		this.props.setAttributes({ recipeTitle: postTitle });

		setTimeout(
			this.setState.bind(this, {
				isPostTitleSet: true,
				isLoading: false,
			}),
			250
		);
	}

	fetchCategories() {
		const {
			attributes: { course },
			categories,
		} = this.props;

		// We have added course
		if (!isEmpty(course)) {
			this.setState({ isLoading: false });
			return;
		}

		// We don't have selected post category
		if (isEmpty(categories)) {
			this.setState({ isLoading: false });
			return;
		}

		const query = {
			...DEFAULT_QUERY,
			...{ include: categories.join(",") },
		};

		this.fetchRequest = apiFetch({
			path: addQueryArgs("/wp/v2/categories", query),
		});

		this.fetchRequest.then(
			(terms) => {
				// resolve
				const availableCategories = map(terms, ({ name }) => {
					return name;
				});

				this.fetchRequest = null;
				this.props.setAttributes({ course: availableCategories });
				setTimeout(
					this.setState.bind(this, {
						isCategoriesFetched: true,
						isLoading: false,
					}),
					250
				);
			},
			(xhr) => {
				// reject
				if (xhr.statusText === "abort") {
					return;
				}
				this.fetchRequest = null;
				this.setState({
					isLoading: false,
				});
			}
		);
	}

	fetchTags() {
		const {
			attributes: { keywords },
			tags,
		} = this.props;

		// We have added keywords
		if (!isEmpty(keywords)) {
			this.setState({ isLoading: false });
			return;
		}

		// We don't have added post tags
		if (isEmpty(tags)) {
			this.setState({ isLoading: false });
			return;
		}

		const query = { ...DEFAULT_QUERY, ...{ include: tags.join(",") } };

		this.fetchRequest = apiFetch({
			path: addQueryArgs("/wp/v2/tags", query),
		});

		this.fetchRequest.then(
			(terms) => {
				// resolve
				const availableTags = map(terms, ({ name }) => {
					return name;
				});

				this.fetchRequest = null;
				this.props.setAttributes({ keywords: availableTags });
				setTimeout(
					this.setState.bind(this, {
						isTagsFetched: true,
						isLoading: false,
					}),
					250
				);
			},
			(xhr) => {
				// reject
				if (xhr.statusText === "abort") {
					return;
				}
				this.fetchRequest = null;
				this.setState({
					isLoading: false,
				});
			}
		);
	}

	/**
	 * Sets the focus to a specific element in block.
	 *
	 * @param {number|string} elementToFocus The element to focus, either the index of the item that should be in focus or name of the input.
	 *
	 * @returns {void}
	 */
	setFocus(elementToFocus) {
		if (elementToFocus === this.state.focus) {
			return;
		}

		this.setState({ focus: elementToFocus });

		if (this.editorRefs[elementToFocus]) {
			this.editorRefs[elementToFocus].focus();
		}
	}

	onSelectImage(media) {
		const relevantMedia = pickRelevantMediaFiles(media, "header");

		this.props.setAttributes({
			hasImage: true,
			image: {
				id: relevantMedia.id,
				url: relevantMedia.url,
				alt: relevantMedia.alt,
				title: relevantMedia.title,
				sizes: media.sizes,
			},
		});
	}

	render() {
		const {
			attributes,
			setAttributes,
			className,
			postType,
			postTitle,
			postAuthor,
		} = this.props;

		const {
			id,
			recipeTitle,
			summary,
			summaryTitle,
			notesTitle,
			notes,
			course,
			cuisine,
			method,
			recipeKey,
			keywords,
			hasVideo,
			video,
			videoTitle,
			hasImage,
			image,
			settings: {
				0: {
					print_btn,
					pin_btn,
					custom_author_name,
					displayCourse,
					displayCuisine,
					displayCookingMethod,
					displayRecipeKey,
					displayAuthor,
				},
			},
		} = attributes;

		const loadingClass = this.state.isLoading ? "is-loading-block" : "";
		const hideRecipeImgClass = "";
		const videoType = get(video, "type");

		let customAuthorName;
		customAuthorName = custom_author_name;
		if (customAuthorName === "") {
			customAuthorName = postAuthor;
		}

		const regex = /is-style-(\S*)/g;
		const m = regex.exec(className);
		const classNames =
			m !== null
				? [
					className,
					`header-content-align`,
					`block-alignment`,
					loadingClass,
					hideRecipeImgClass,
				]
				: [
					className,
					`header-content-align`,
					`block-alignment`,
					loadingClass,
					hideRecipeImgClass,
				];

		const RecipeCardClassName = classNames.filter((item) => item).join(" ");

		return (
			<div id={id} className={RecipeCardClassName}>
				{this.state.isLoading && (
					<Placeholder
						className="delicious-recipes-loading-spinner"
						label={__("Loading...", "delicious-recipes")}
					>
						<Spinner />
					</Placeholder>
				)}
				<div id="dr-recipe-meta-main" className="dr-summary-holder"
					style={{
						background: `rgba(${setting_options.primaryColorRGB}, 0.05)`,
					}}
				>
					<div className="dr-post-summary">
						<div className="dr-recipe-summary-inner">
							<div className="dr-image">
								{!hasImage && (
									<Placeholder
										icon="format-image"
										className="recipe-card-image-placeholder"
										label={__(
											"Recipe Image",
											"delicious-recipes"
										)}
										instructions={__(
											"Select an image file from your library.",
											"delicious-recipes"
										)}
									>
										<MediaUpload
											onSelect={this.onSelectImage}
											allowedTypes={ALLOWED_MEDIA_TYPES}
											value="0"
											render={({ open }) => (
												<Button
													onClick={open}
													isSecondary="true"
												>
													{__(
														"Media Library",
														"delicious-recipes"
													)}
												</Button>
											)}
										/>
									</Placeholder>
								)}
								{hasImage && (
									<Fragment>
										<img
											src={get(image, ["url"])}
											id={get(image, ["id"])}
											alt={recipeTitle}
										/>
										{pin_btn && (
											<span className="post-pinit-button">
												<img
													src={
														pluginURL +
														"/assets/images/pinit-sm.png"
													}
												/>
											</span>
										)}
										{print_btn && (
											<div className="dr-buttons"
												style={{
													backgroundColor: delrcp.setting_options.primaryColor,
												}}
											>
												{__("Print Recipe", "delicious-recipes")}
											</div>
										)}
									</Fragment>
								)}
							</div>
							<div className="dr-title-wrap">
								<RichText
									className="dr-title recipe-card-title"
									tagName="h2"
									format="string"
									value={recipeTitle}
									unstableOnFocus={() =>
										this.setFocus("recipeTitle")
									}
									onChange={(newTitle) =>
										setAttributes({ recipeTitle: newTitle })
									}
									// onSetup={(ref) => {
									// 	this.editorRefs.recipeTitle = ref;
									// }}
									placeholder={__(
										"Enter the title of your recipe.",
										"delicious-recipes"
									)}
								/>

								<div className="dr-entry-meta">
									{displayAuthor && (
										<span className="dr-byline">
											<span className="dr-meta-title">
												{icons.author}
												{__("Author:", "delicious-recipes")}
											</span>
											{customAuthorName}
										</span>
									)}

									{displayCookingMethod && (
										<span className="dr-method">
											<span className="dr-meta-title">
												{icons.cookingmethod}
												{__("Cooking Method:", "delicious-recipes")}
											</span>
											{!RichText.isEmpty(method)
												? method
													.filter((item) => item)
													.join(", ")
												: __(
													"Not added",
													"delicious-recipes"
												)}
										</span>
									)}

									{displayCuisine && (
										<span className="dr-cuisine">
											<span className="dr-meta-title">
												{icons.cuisine}
												{__("Cuisine:", "delicious-recipes")}
											</span>
											{!RichText.isEmpty(cuisine)
												? cuisine
													.filter((item) => item)
													.join(", ")
												: __(
													"Not added",
													"delicious-recipes"
												)}
										</span>
									)}

									{displayCourse && (
										<span className="dr-course">
											<span className="dr-meta-title">
												{icons.courses}
												{__("Courses:", "delicious-recipes")}
											</span>
											{!RichText.isEmpty(course)
												? course
													.filter((item) => item)
													.join(", ")
												: __(
													"Not added",
													"delicious-recipes"
												)}
										</span>
									)}

									{displayRecipeKey && (
										<span className="dr-category dr-recipe-keys">
											<span className="dr-meta-title">
												{icons.recipekey}
												{__("Recipe Keys:", "delicious-recipes")}
											</span>
											{!RichText.isEmpty(recipeKey)
												? recipeKey
													.filter((item) => item)
													.join(", ")
												: __(
													"Not added",
													"delicious-recipes"
												)}
										</span>
									)}
								</div>
							</div>
						</div>
						<Detail
							generateId={generateId}
							{...{ attributes, setAttributes, className }}
						/>
						<div className="dr-summary recipe-card-summary">
							<RichText
								tagName="h3"
								className="dr-title summary-title"
								format="string"
								value={summaryTitle}
								unstableOnFocus={() =>
									this.setFocus("summaryTitle")
								}
								onChange={(summaryTitle) =>
									setAttributes({ summaryTitle })
								}
								// onSetup={(ref) => {
								// 	this.editorRefs.summaryTitle = ref;
								// }}
								placeholder={__(
									"Write Summary title",
									"delicious-recipes"
								)}
							/>
							<RichText
								tagName="p"
								value={summary}
								unstableOnFocus={() => this.setFocus("summary")}
								onChange={(newSummary) =>
									setAttributes({
										summary: newSummary,
										jsonSummary: stripHTML(
											renderToString(newSummary)
										),
									})
								}
								// onSetup={(ref) => {
								// 	this.editorRefs.summary = ref;
								// }}
								placeholder={__(
									"Enter a short recipe description.",
									"delicious-recipes"
								)}
							/>
						</div>
					</div>

					<Ingredient
						generateId={generateId}
						{...{ attributes, setAttributes, className }}
					/>
					<Direction
						generateId={generateId}
						{...{ attributes, setAttributes, className }}
					/>

					<div className="recipe-card-video">
						<RichText
							tagName="h3"
							className="video-title"
							format="string"
							value={videoTitle}
							unstableOnFocus={() => this.setFocus("videoTitle")}
							onChange={(videoTitle) =>
								setAttributes({ videoTitle })
							}
							// onSetup={(ref) => {
							// 	this.editorRefs.videoTitle = ref;
							// }}
							placeholder={__(
								"Write Recipe Video title",
								"delicious-recipes"
							)}
						/>
						{!hasVideo && (
							<Placeholder
								icon="video-alt3"
								className="delicious-recipes-video-placeholder"
								instructions={__(
									"You can add a video here from Recipe Card Video Settings in the right sidebar",
									"delicious-recipes"
								)}
								label={__(
									"Recipe Card Video",
									"delicious-recipes"
								)}
							/>
						)}
						{hasVideo && "embed" === videoType && (
							<Fragment>
								<ReactPlayer
									width="100%"
									height="340px"
									url={get(video, "url")}
								/>
							</Fragment>
						)}
						{hasVideo && "self-hosted" === videoType && (
							<Fragment>
								<video
									controls={get(video, "settings.controls")}
									poster={get(video, "poster.url")}
									src={get(video, "url")}
								/>
							</Fragment>
						)}
					</div>

					<div className="dr-note">
						<RichText
							tagName="h3"
							className="dr-title notes-title"
							format="string"
							value={notesTitle}
							unstableOnFocus={() => this.setFocus("notesTitle")}
							onChange={(notesTitle) =>
								setAttributes({ notesTitle })
							}
							// onSetup={(ref) => {
							// 	this.editorRefs.notesTitle = ref;
							// }}
							placeholder={__(
								"Write Notes title",
								"delicious-recipes"
							)}
						/>
						<RichText
							className="recipe-card-notes-list"
							tagName="ul"
							multiline="li"
							value={notes}
							unstableOnFocus={() => this.setFocus("notes")}
							onChange={(newNote) =>
								setAttributes({ notes: newNote })
							}
							// onSetup={(ref) => {
							// 	this.editorRefs.notes = ref;
							// }}
							placeholder={__(
								"Enter Note text for your recipe.",
								"delicious-recipes"
							)}
						/>
						<p className="description">
							{__(
								"Press Enter to add new note.",
								"delicious-recipes"
							)}
						</p>
					</div>

					<div className="dr-keywords">
						<span className="dr-meta-title">{__("Keywords:", "delicious-recipes")}</span>
						{!RichText.isEmpty(keywords)
							? keywords.filter((item) => item).join(", ")
							: __("Not added", "delicious-recipes")}
					</div>
					<Inspector
						media={this.props.media}
						categories={this.props.categories}
						postTitle={postTitle}
						postType={postType}
						postAuthor={postAuthor}
						imageSizes={this.props.imageSizes}
						maxWidth={this.props.maxWidth}
						isRTL={this.props.isRTL}
						{...{ attributes, setAttributes, className }}
					/>
				</div>
			</div>
		);
	}
}

export default compose([
	withSelect((select, props) => {
		const {
			attributes: { image, hasImage },
		} = props;

		const { getMedia, getPostType, getAuthors } = select("core");

		const {
			getEditorSettings,
			getEditedPostAttribute,
			getPermalink,
		} = select("core/editor");

		const { maxWidth, isRTL, imageSizes } = getEditorSettings();

		const getAuthorData = (authors, path = "") => {
			const postAuthor = getEditedPostAttribute("author");
			let authorData = null;

			authors.map(function (author, key) {
				if (author.id === postAuthor) {
					if (path !== "") {
						authorData = get(authors, [key, path]);
					} else {
						authorData = get(authors, [key]);
					}
				}
			});

			return authorData;
		};

		const postType = getPostType(getEditedPostAttribute("type"));
		const postPermalink = getPermalink();
		const categories = getEditedPostAttribute("categories");
		const tags = getEditedPostAttribute("tags");
		const postTitle = getEditedPostAttribute("title");
		const featuredImageId = getEditedPostAttribute("featured_media");
		const authors = getAuthors();
		const postAuthor = getAuthorData(authors, "name");

		let id = 0;

		if (hasImage) {
			id = get(image, ["id"]) || 0;
		} else {
			id = featuredImageId;
		}

		return {
			media: id ? getMedia(id) : false,
			postTitle,
			postType,
			postAuthor,
			postPermalink,
			categories,
			tags,
			imageSizes,
			maxWidth,
			isRTL,
		};
	}),
])(RecipeCard);
