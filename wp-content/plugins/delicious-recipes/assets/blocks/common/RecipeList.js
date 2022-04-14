const { decodeEntities } = wp.htmlEntities;
import { __ } from "@wordpress/i18n";
import icons from "./icons";

const { Component, Fragment } = wp.element;

export class RecipeList extends Component {
	constructor() {
		super(...arguments);
	}

	render() {
		const { recipe, preventClick } = this.props;
		return (
			<Fragment>
				<article className="recipe-post">
					<figure className="post-thumbnail">
						<a
							dangerouslySetInnerHTML={{
								__html:
									"" === recipe.thumbnail_url
										? recipe.fallback_svg
										: `<img
							src="${recipe.thumbnail_url}"
							alt="${decodeEntities(recipe.title)}"
						/>`,
							}}
							onClick={preventClick}
							href={recipe.permalink}
						></a>

						{recipe.recipe_badges && (
							<span className="dr-badge">
								<a
									onClick={preventClick}
									href={recipe.recipe_badges.link}
									title={recipe.recipe_badges.badge}
									style={{
										backgroundColor:
											recipe.recipe_badges.color,
									}}
									dangerouslySetInnerHTML={{
										__html: recipe.recipe_badges.badge,
									}}
								></a>
							</span>
						)}

						{recipe.recipe_keys.length > 0 && (
							<span className="dr-category">
								{recipe.recipe_keys.map((recipe_key, index) => (
									<a
										key={index}
										dangerouslySetInnerHTML={{
											__html: recipe_key.icon,
										}}
										onClick={preventClick}
										href={recipe_key.link}
										title={recipe_key.key}
									></a>
								))}
							</span>
						)}
					</figure>
					<div className="content-wrap">
						<header className="entry-header">
							{recipe.recipe_course.length > 0 && (
								<span className="post-cat">
									{recipe.recipe_course.map(
										(recipe_course, key) => (
											<a
												key={key}
												onClick={preventClick}
												href={recipe_course.link}
												title={recipe_course.key}
												dangerouslySetInnerHTML={{
													__html: recipe_course.key,
												}}
											></a>
										)
									)}
								</span>
							)}
							<h3 className="entry-title">
								<a
									onClick={preventClick}
									href={recipe.permalink}
								>
									{decodeEntities(recipe.title)}
								</a>
							</h3>
							<div className="entry-meta">
								<span className="posted-on">
									<a onClick={preventClick} href="#">
										{icons.calendar}
										<time>{recipe.date_published}</time>
									</a>
								</span>
								<span className="comment">
									<a href="#">
										{icons.comment}
										<span className="meta-text">
											{recipe.comments_number}
											{__(
												" Comments",
												"delicious-recipes"
											)}
										</span>
									</a>
								</span>
							</div>
						</header>
						<div
							className="entry-content"
							dangerouslySetInnerHTML={{
								__html: recipe.description,
							}}
						></div>
						<footer className="entry-footer">
							<span className="byline">
								<a href="#">
									<img
										src={recipe.author_avatar}
										alt={recipe.author}
									/>
									<b className="fn">{recipe.author}</b>
								</a>
							</span>
							{recipe.total_time && (
								<span className="cook-time">
									{icons.time}
									<span className="meta-text">
										{recipe.total_time}
									</span>
								</span>
							)}
							{recipe.difficulty_level && (
								<span className="cook-difficulty">
									{icons.difficulty}
									<span className="meta-text">
										{recipe.difficulty_level}
									</span>
								</span>
							)}
						</footer>
					</div>
				</article>
			</Fragment>
		);
	}
}
