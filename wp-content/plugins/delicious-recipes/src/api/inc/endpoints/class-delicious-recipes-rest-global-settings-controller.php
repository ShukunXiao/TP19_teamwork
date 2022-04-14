<?php
/**
 * REST API: Delicious_Recipes_REST_Global_Settings_Controller class
 *
 * @package Delicious Recipes API Core
 * @subpackage API Core
 * @since 1.0.0
 */

/**
 * Core base controller for managing and interacting with Global Recipe Settings.
 *
 * @since 1.0.0
 */
class Delicious_Recipes_REST_Global_Settings_Controller extends Delicious_Recipes_API_Controller {

	/**
	 * Constructor
	 */
	public function __construct( $post_type ) {
		$this->base_name = '/recipe-global';
	}

	// Register our routes.
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			$this->base_name,
			array(
				// Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'post_settings' ),
					'permission_callback' => array( $this, 'update_settings_permissions_check' ),
				),
				// Register our schema callback.
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			$this->base_name . '/onboard',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'post_onboard_settings' ),
					'permission_callback' => array( $this, 'update_settings_permissions_check' ),
				),
				// Register our schema callback.
				'schema' => array( $this, 'get_item_schema' ),
			)
		);
	}

	/**
	 * Grabs Global recipe settings.
	 *
	 * @param $request WP_REST_Request Current request.
	 */
	public function get_settings( $request ) {

		$global_settings = delicious_recipes_get_global_settings();
		
		if ( empty( $global_settings ) ) {
			return rest_ensure_response(
				array(
					'success' => false,
					'message' => __( 'Recipe Settings not found.' , 'delicious-recipes' ),
					'data'    => array(),
				)
			);
		}

		$data = array(
			'success' => true,
			'message' => __( 'Recipe Settings Found.', 'delicious-recipes' ),
		);

		$response     = $this->prepare_item_for_response( $request );
		$data['data'] = $this->prepare_response_for_collection( $response );
		
		// Return all of our post response data.
		return $data;
	}

	/**
	 * Saves Recipe Global Settings.
	 *
	 * @param $request WP_REST_Request Current request.
	 */
	public function post_settings( $request ) {

		$formdata = $request->get_json_params();
		$formdata = stripslashes_deep( $formdata );

		// Sanitize and save settings.
		$sanitized_settings = $this->sanitize_settings( $formdata );

		$settings = get_option( 'delicious_recipe_settings', array() );
		if( ( $settings['recipeBase'] != $sanitized_settings['recipeBase'] ) || 
			( $settings['courseBase'] != $sanitized_settings['courseBase'] ) || 
			( $settings['cuisineBase'] != $sanitized_settings['cuisineBase'] ) || 
			( $settings['cookingMethodBase'] != $sanitized_settings['cookingMethodBase'] ) ||
			( $settings['keyBase'] != $sanitized_settings['keyBase'] ) || 
			( $settings['tagBase'] != $sanitized_settings['tagBase'] ) ||
			( $settings['badgeBase'] != $sanitized_settings['badgeBase'] )
		) {
			update_option( 'delicious_recipes_queue_flush_rewrite_rules', 'yes' );
		}
		
		update_option( 'delicious_recipe_settings', $sanitized_settings );

		$data = array(
			'success'  => true,
			'message'  => __( 'Recipe Global Settings Saved Successfully.', 'delicious-recipes' )
		);

		// Return all of our post response data.
		return $data;
	}

	/**
	 * Saves Recipe Global Settings on onboarding.
	 *
	 * @param $request WP_REST_Request Current request.
	 */
	public function post_onboard_settings( $request ) {

		$formdata = $request->get_json_params();
		$formdata = stripslashes_deep( $formdata );

		// Sanitize and save settings.
		$sanitized_settings = $this->sanitize_settings( $formdata );
		if ( ! empty( $sanitized_settings ) ) {
			foreach( $sanitized_settings as $key => $value ) {
				delicious_recipes_set_recipe_setting( $key, $value );
			}
		}

		$data = array(
			'success'  => true,
			'message'  => __( 'Recipe Onboarding Settings Saved Successfully.', 'delicious-recipes' )
		);

		// Return all of our post response data.
		return $data;
	}


	/**
	 * Settings data sanitization.
	 *
	 * @param [type] $settings_data
	 * @return void
	 */
	public function sanitize_settings( $settings_data ) {

		if ( ! empty( $settings_data ) ) {
			foreach( $settings_data as $key => $setting ) {
				if( ! is_array( $setting ) ) {
					if( 'primaryColor' === $key || 'secondaryColor' === $key ) {
						$settings_data[$key] = sanitize_hex_color( $setting );
					} elseif( 'thankyouMessage' === $key || 'authorDescription' === $key 
					|| 'newAccountContent' === $key || 'resetPasswordContent' === $key 
					|| 'recipeSubmissionGuideline' === $key || 'recipeDisclaimer' === $key 
					|| 'newRecipeSubmissionContent' === $key || 'recipePublishedContent' === $key || 'affiliateDisclosure' === $key ) {
						$settings_data[$key] = wp_kses_post( $setting );
					} elseif( 'printLogoImagePreview' === $key || 'authorImagePreview' === $key 
					|| 'loginImagePreview' === $key || 'registrationImagePreview' === $key ) {
						$settings_data[$key] = esc_url( $setting );
					} else {
						$settings_data[$key] = sanitize_text_field( $setting );
					}
				} else {
					foreach( $setting as $sub_key => $sub_setting ) {
						if ( ! is_array( $sub_setting ) ) {
							$settings_data[$key][$sub_key] = sanitize_text_field( $sub_setting );
						} else {
							foreach( $sub_setting as $sub_sub_key => $sub_sub_setting ) {
								if ( ! is_array( $sub_sub_setting ) ) {
									if ( 'content' === $sub_sub_key ) {
										$settings_data[$key][$sub_key][$sub_sub_key] = wp_kses_post( $sub_sub_setting );
									} else {
										$settings_data[$key][$sub_key][$sub_sub_key] = sanitize_text_field( $sub_sub_setting );
									}
								} else {
									foreach( $sub_sub_setting as $sub_sub_sub_key => $sub_sub_sub_setting ) {
										if ( ! is_array( $sub_sub_sub_setting ) ) {
											$settings_data[$key][$sub_key][$sub_sub_key][$sub_sub_sub_key] = sanitize_text_field( $sub_sub_sub_setting );
										}
									}
								}
							}
						}
					}
				}
			}
		}

		return $settings_data;
	}

	/**
	 * Matches the post data to the schema we want.
	 *
	 * @param WP_Post $post The comment object whose response is being prepared.
	 */
	public function prepare_item_for_response( $request ) {

		$schema = $this->get_item_schema( $request );
		$fields = $this->get_fields_for_response( $request );

		$recipe_settings = delicious_recipes_get_global_settings();

		$data = array(
			'recipe_settings' => $recipe_settings
		);

		return rest_ensure_response( $data );
	}

	/**
	 * Prepare a response for inserting into a collection of responses.
	 *
	 * This is copied from WP_REST_Controller class in the WP REST API v2 plugin.
	 *
	 * @param WP_REST_Response $response Response object.
	 * @return array Response data, ready for insertion into collection data.
	 */
	public function prepare_response_for_collection( $response ) {
		if ( ! ( $response instanceof WP_REST_Response ) ) {
			return $response;
		}

		$data   = (array) $response->get_data();
		$server = rest_get_server();

		if ( method_exists( $server, 'get_compact_response_links' ) ) {
			$links = call_user_func( array( $server, 'get_compact_response_links' ), $response );
		} else {
			$links = call_user_func( array( $server, 'get_response_links' ), $response );
		}

		if ( ! empty( $links ) ) {
			$data['_links'] = $links;
		}

		return $data;
	}

	/**
	 * Retrieves the query params for the collections.
	 *
	 * @since 1.0.0
	 *
	 * @return array Query parameters for the collection.
	 */
	public function get_collection_params() {
		return array(
			'page'     => array(
				'description'       => __( 'Current page of the collection.', 'delicious-recipes' ),
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
				'minimum'           => 1,
			),
			'per_page' => array(
				'description'       => __( 'Maximum number of items to be returned in result set.', 'delicious-recipes' ),
				'type'              => 'integer',
				'default'           => 10,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
		);
	}

	/**
	 * Checks the post_date_gmt or modified_gmt and prepare any post or
	 * modified date for single post output.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $date_gmt GMT publication time.
	 * @param string|null $date     Optional. Local publication time. Default null.
	 * @return string|null ISO8601/RFC3339 formatted datetime.
	 */
	protected function prepare_date_response( $date_gmt, $date = null ) {
		// Use the date if passed.
		if ( isset( $date ) ) {
			return mysql_to_rfc3339( $date );
		}

		// Return null if $date_gmt is empty/zeros.
		if ( '0000-00-00 00:00:00' === $date_gmt ) {
			return null;
		}

		// Return the formatted datetime.
		return mysql_to_rfc3339( $date_gmt );
	}

	/**
	 * Get our sample schema for a post.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function get_item_schema( $request = null ) {
		$schema = array(
			// This tells the spec of JSON Schema we are using which is draft 4.
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			// The title property marks the identity of the resource.
			'title' => DELICIOUS_RECIPE_POST_TYPE,
			'type'  => 'object',
			// In JSON Schema you can specify object properties in the properties attribute.
			'properties' => array(
				'id' => array(
					'description' => __( 'Unique identifier for the object.', 'delicious-recipes' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'date' => array(
					'description' => __( "The date the object was published, in the site's timezone.", 'delicious-recipes' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'title' => array(
					'description' => __( 'The title for the object.', 'delicious-recipes' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit', 'embed' ),
					'arg_options' => array(
						'sanitize_callback' => null,   // Note: sanitization implemented in self::prepare_item_for_database()
						'validate_callback' => null,   // Note: validation implemented in self::prepare_item_for_database()
					),
					'properties'  => array(
						'raw' => array(
							'description' => __( 'Title for the object, as it exists in the database.', 'delicious-recipes' ),
							'type'        => 'string',
							'context'     => array( 'edit' ),
						),
						'rendered' => array(
							'description' => __( 'HTML title for the object, transformed for display.', 'delicious-recipes' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit', 'embed' ),
							'readonly'    => true,
						),
					),
				),
				'link'            => array(
					'description' => __( 'URL to the object.', 'delicious-recipes' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'modified'        => array(
					'description' => __( "The date the object was last modified, in the site's timezone.", 'delicious-recipes' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'modified_gmt'    => array(
					'description' => __( 'The date the object was last modified, as GMT.', 'delicious-recipes' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'status'          => array(
					'description' => __( 'A named status for the object.', 'delicious-recipes' ),
					'type'        => 'string',
					'enum'        => array_keys( get_post_stati( array( 'internal' => false ) ) ),
					'context'     => array( 'view', 'edit' ),
				),
				'type'            => array(
					'description' => __( 'Type of Post for the object.', 'delicious-recipes' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Adds the schema from additional fields to a schema array.
	 *
	 * The type of object is inferred from the passed schema.
	 *
	 * @since 1.0.0
	 *
	 * @param array $schema Schema array.
	 * @return array Modified Schema array.
	 */
	protected function add_additional_fields_schema( $schema ) {
		if ( empty( $schema['title'] ) ) {
			return $schema;
		}

		// Can't use $this->get_object_type otherwise we cause an inf loop.
		$object_type = $schema['title'];

		return $schema;
	}

}

// Function to register our new routes from the controller.
function delicious_recipe_register_global_settings_rest_routes() {
	$controller = new Delicious_Recipes_REST_Global_Settings_Controller( DELICIOUS_RECIPE_POST_TYPE );
	$controller->register_routes();
}

add_action( 'rest_api_init', 'delicious_recipe_register_global_settings_rest_routes' );
