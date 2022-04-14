<?php
/**
 * Class Helpers functions
 *
 * @since   1.0.4
 * @package Delicious_Recipes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for helper functions for structured data render.
 */
class Delicious_Recipes_Helpers {
    public function generateId( $prefix = '') {
		return $prefix !== '' ? uniqid( $prefix . '-' ) : uniqid();
	}

	public function render_attributes( $attributes ) {
		if ( empty( $attributes ) )
			return '';

		$render = '';

		if ( is_array( $attributes ) ) {
			foreach ( $attributes as $property => $value ) {
				$render .= sprintf( '%s="%s" ', $property, esc_attr( $value ) );
			}
		} elseif ( is_string( $attributes ) ) {
			$render = $attributes;
		}
		return trim( $render );
	}

	public function render_styles_attributes( $styles ) {
		if ( empty( $styles ) )
			return '';

		$render = '';
		
		if ( is_array( $styles ) ) {
			foreach ( $styles as $property => $value ) {
				$render .= sprintf( '%s: %s; ', $property, $value );
			}
		} elseif ( is_string( $styles ) ) {
			$render = $styles;
		}
		return trim( $render );
	}

	public function parse_block_settings( $attrs ) {
		$settings = isset( $attrs['settings'][0] ) ? $attrs['settings'][0] : array();
		
		if ( !isset( $settings['custom_author_name'] ) ) {
			$settings['custom_author_name'] = '';
		}
		if ( !isset( $settings['displayServings'] ) ) {
			$settings['displayServings'] = true;
		}
		if ( !isset( $settings['displayCourse'] ) ) {
			$settings['displayCourse'] = true;
		}
		if ( !isset( $settings['displayCuisine'] ) ) {
			$settings['displayCuisine'] = true;
		}
		if ( !isset( $settings['displayCookingMethod'] ) ) {
			$settings['displayCookingMethod'] = true;
		}
		if ( !isset( $settings['displayRecipeKey'] ) ) {
			$settings['displayRecipeKey'] = true;
		}
		if ( !isset( $settings['displayDifficulty'] ) ) {
			$settings['displayDifficulty'] = true;
		}
		if ( !isset( $settings['displayAuthor'] ) ) {
			$settings['displayAuthor'] = true;
		}
		if ( !isset( $settings['displayPrepTime'] ) ) {
			$settings['displayPrepTime'] = true;
		}
		if ( !isset( $settings['displayCookingTime'] ) ) {
			$settings['displayCookingTime'] = true;
		}
		if ( !isset( $settings['displayRestTime'] ) ) {
			$settings['displayRestTime'] = true;
		}
		if ( !isset( $settings['displayTotalTime'] ) ) {
			$settings['displayTotalTime'] = true;
		}
		if ( !isset( $settings['displayCalories'] ) ) {
			$settings['displayCalories'] = true;
		}
		if ( !isset( $settings['displayBestSeason'] ) ) {
			$settings['displayBestSeason'] = true;
		}

		if ( !isset( $settings['print_btn'] ) ) {
			$settings['print_btn'] = true;
		}
		if ( !isset( $settings['pin_btn'] ) ) {
			$settings['pin_btn'] = true;
		}

		return $settings;
	}

	public function parse_recipe_buttons_block_settings( $attrs ) {
		$settings = isset( $attrs['settings'][0] ) ? $attrs['settings'][0] : array();
		
		if ( !isset( $settings['jump_to_recipe_btn'] ) ) {
			$settings['jump_to_recipe_btn'] = true;
		}
		if ( !isset( $settings['jump_to_video_btn'] ) ) {
			$settings['jump_to_video_btn'] = true;
		}
		if ( !isset( $settings['print_recipe_btn'] ) ) {
			$settings['print_recipe_btn'] = true;
		}
		
		return $settings;
	}

	public function omit( array $array, array $paths ) {
		foreach ( $array as $key => $value ) {
			if ( in_array( $key, $paths ) ) {
				unset( $array[ $key ] );
			}
		}

		return $array;
	}

	public function getNumberFromString( $string ) {
		if ( ! is_string( $string ) ) {
			return false;
		}
		preg_match('/\d+/', $string, $matches);
		return $matches ? $matches[0] : 0;
	}

	public function convertMinutesToHours( $minutes, $returnArray = false ) {
		$output = '';
		$time = $this->getNumberFromString( $minutes );

		if ( ! $time ) {
			return $minutes;
		}
		
		$hours = floor( $time / 60 );
		$mins = ( $time % 60 );

		if ( $returnArray ) {
			if ( $hours ) {
				$array['hours']['value'] = $hours;
				$array['hours']['unit'] = _n( "hour", "hours", (int)$hours, "delicious-recipes" );
			}
			if ( $mins ) {
				$array['minutes']['value'] = $mins;
				$array['minutes']['unit'] = _n( "minute", "minutes", (int)$mins, "delicious-recipes" );
			}

			return $array;
		}

		if ( $hours ) {
			$output = $hours .' '. _n( "hour", "hours", (int)$hours, "delicious-recipes" );
		}

		if ( $mins ) {
			$output .= ' ' . $mins .' '. _n( "minute", "minutes", (int)$mins, "delicious-recipes" );
		}

		return $output;
	}

	public function convert_youtube_url_to_embed( $url ) {
		$embed_url = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i","https://www.youtube.com/embed/$1?feature=oembed", $url );
		return $embed_url;
	}

	public function convert_vimeo_url_to_embed( $url ) {
		$embed_url = preg_replace("/\s*[a-zA-Z\/\/:\.]*vimeo.com\/([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i","https://player.vimeo.com/video/$1", $url );
		return $embed_url;
	}
}

