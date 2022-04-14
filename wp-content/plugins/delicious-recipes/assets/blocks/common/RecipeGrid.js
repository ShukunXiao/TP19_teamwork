const { decodeEntities } = wp.htmlEntities;
import { __ } from "@wordpress/i18n";
import icons from "./icons";

const { Component, Fragment } = wp.element;

export class RecipeGrid extends Component {
	constructor() {
		super(...arguments);
	}

	render() {
		const { recipe, preventClick } = this.props;
		return (
			<Fragment>
				<div className="dr-archive-single">
					<figure>
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
					<div className="dr-archive-details">
						<h2 className="dr-archive-list-title">
							<a onClick={preventClick} href={recipe.permalink}>
								{decodeEntities(recipe.title)}
							</a>
						</h2>
						<div className="dr-entry-meta">
							{recipe.total_time && (
								<span className="dr-time">
									{icons.time}
									<span className="dr-meta-title">
										{recipe.total_time}
									</span>
								</span>
							)}
							{recipe.difficulty_level && (
								<span className="dr-level">
									{icons.difficulty}
									<span className="dr-meta-title">
										{recipe.difficulty_level}
									</span>
								</span>
							)}
						</div>
					</div>
				</div>
			</Fragment>
		);
	}
}
