<?php
/**
 * REST API: Delicious_Recipes_REST_Recipe_Controller class
 *
 * @package Delicious Recipes API Core
 * @subpackage API Core
 * @since 1.0.0
 */

/**
 * Core base controller for managing and interacting with Recipes.
 *
 * @since 1.0.0
 */
class Delicious_Recipes_REST_Recipe_Controller extends Delicious_Recipes_API_Controller {

	/**
	 * Constructor
	 */
	public function __construct( $post_type ) {
		$this->base_name = $post_type;
	}

	// Register our routes.
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->base_name,
			array(
				// Here we register the readable endpoint for collections.
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				// Register our schema callback.
				'schema' => array( $this, 'get_item_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->base_name . '/(?P<id>[\d]+)',
			array(
				// Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'post_item' ),
					'permission_callback' => array( $this, 'post_item_permissions_check' ),
				),
				// Register our schema callback.
				'schema' => array( $this, 'get_item_schema' ),
			)
		);
	}

	/**
	 * Grabs the five most recent posts and outputs them as a rest response.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function get_items( $request ) {
		// Retrieve the list of registered collection query parameters.
		$registered = $this->get_collection_params();
		$mappings = array(
			'per_page' => 'posts_per_page',
			'page' => 'paged',
		);
		$args  = array(
			'posts_per_page' => 10,
			'post_type'     => $this->base_name,
		);
		foreach ( $mappings as $api_param => $wp_param ) {
			if( isset( $registered[ $api_param ], $request[ $api_param ] ) ){
				$args[ $wp_param ] = $request[ $api_param ];
			}
		}
		$posts_query = new WP_Query( $args );

		$posts = $posts_query->get_posts();

		$data = array(
			'success' => true,
			'message' => __( 'Recipes Found.', 'delicious-recipes' ),
			'data'    => array(),
		);

		if ( empty( $posts ) ) {
			return rest_ensure_response(
				array(
					'success' => false,
					'message' => __( 'No Recipes Found.', 'delicious-recipes' ),
					'data'    => array(),
				)
			);
		}

		foreach ( $posts as $post ) {
			$response       = $this->prepare_item_for_response( $post, $request );
			$data['data'][] = $this->prepare_response_for_collection( $response );
		}

		$response = rest_ensure_response( $data );

		$response->header( 'X-WP-Total', (int) $posts_query->found_posts );
		$response->header( 'X-WP-TotalPages', (int) $posts_query->max_num_pages );

		// Return all of our comment response data.
		return $response;
	}

	/**
	 * Grabs a single Enquiry if vald id is provided.
	 *
	 * @param $request WP_REST_Request Current request.
	 */
	public function get_item( $request ) {
		$id   = (int) $request['id'];
		$post = get_post( $id );

		$data = array(
			'success' => true,
			'message' => __( 'Recipe Found.', 'delicious-recipes' ),
		);
		if ( empty( $post ) ) {
			return rest_ensure_response(
				array(
					'success' => false,
					'message' => __( 'Recipe not found by ID.' , 'delicious-recipes' ),
					'data'    => array(),
				)
			);
		}

		$response     = $this->prepare_item_for_response( $post, $request );
		$data['data'] = $this->prepare_response_for_collection( $response );
		// Return all of our post response data.
		return $data;
	}

	/**
	 * Grabs a single Enquiry if vald id is provided.
	 *
	 * @param $request WP_REST_Request Current request.
	 */
	public function post_item( $request ) {

		if ( ! isset( $request['id'] ) ) {
			return rest_ensure_response(
				array(
					'success' => false,
					'message' => __( 'Recipe not found by ID.' , 'delicious-recipes' ),
					'data'    => array(),
				)
			);
		}

		$id   = (int) $request['id'];
		$post = get_post( $id );

		$data = array(
			'success'  => true,
			'message'  => __( 'Recipe Settings Saved Successfully.', 'delicious-recipes' ),
			'post_url' => get_the_permalink( $id )
		);
		if ( empty( $post ) ) {
			return rest_ensure_response(
				array(
					'success' => false,
					'message' => __( 'Recipe not found by ID.' , 'delicious-recipes' ),
					'data'    => array(),
				)
			);
		}

		$formdata = $request->get_json_params();

		// Sanitize data and update.
		$sanitized_recipe_metas = delicious_recipes_sanitize_metas( $formdata );
		update_post_meta( $id, 'delicious_recipes_metadata', $sanitized_recipe_metas );

		// update seasons 
		if( isset( $formdata['bestSeason'] ) ) {
			update_post_meta( $id, '_dr_best_season', sanitize_text_field( $formdata['bestSeason'] ) );
		}

		// update difficulty levels
		if( isset( $formdata['difficultyLevel'] ) ) {
			update_post_meta( $id, '_dr_difficulty_level', sanitize_text_field( $formdata['difficultyLevel'] ) );
		}

		// update ingredients
		$ingredients = delicious_recipes_get_single_ingredients( $id );

		if( ! empty( $ingredients ) && is_array( $ingredients ) ) {
			$ingredients = array_map( 'sanitize_text_field', $ingredients );
			update_post_meta( $id, '_dr_recipe_ingredients', $ingredients );
			
			$ingredient_count = count( $ingredients );
			update_post_meta( $id, '_dr_ingredient_count', absint( $ingredient_count ) );
		}

		// Return all of our post response data.
		return $data;
	}

	/**
	 * Matches the post data to the schema we want.
	 *
	 * @param WP_Post $post The comment object whose response is being prepared.
	 */
	public function prepare_item_for_response( $post, $request ) {

		$schema = $this->get_item_schema( $request );
		$fields = $this->get_fields_for_response( $request );

		$recipe_metas = get_post_meta( $post->ID, 'delicious_recipes_metadata', true );

		$data = array(
			'id'           => $post->ID,
			'recipe_metas' => $recipe_metas
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
	 * @since 4.7.0
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
function delicious_recipe_register_recipe_rest_routes() {
	$controller = new Delicious_Recipes_REST_Recipe_Controller( DELICIOUS_RECIPE_POST_TYPE );
	$controller->register_routes();
}

add_action( 'rest_api_init', 'delicious_recipe_register_recipe_rest_routes' );
