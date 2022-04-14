<?php
/**
 * Frontend Template Hooks
 *
 * @package Delicious_Recipes
 */
class Delicious_Recipes_Template_Hooks {

    private static $_instance = null;

    private function __construct() {
        $this->init_hooks();

    }

    public static function get_instance(){
        if( null === self::$_instance){
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * Initialization hooks.
     *
     * @return void
     */
    private function init_hooks() {

        // Single recipe page hooks.
        $this->init_single_recipe_hooks();

        // Archive template hooks.
        $this->init_archive_recipe_hooks();

        // Recipe search page hooks.
        $this->init_recipe_search_hooks();
    }

    /**
     * Recipe single page template hooks.
     *
     * @return void
     */
    private function init_single_recipe_hooks() {
        add_action( 'delicious_recipes_before_main_content', array( $this, 'recipe_main_wrap_start' ) );
        add_action( 'delicious_recipes_primary_wrap_start', array( $this, 'recipe_primary_wrap_start' ) );
        add_action( 'delicious_recipes_primary_wrap_end', array( $this, 'recipe_primary_wrap_end' ) );
        add_action( 'delicious_recipes_after_main_content', array( $this, 'recipe_main_wrap_end' ) );
        add_action( 'delicious_recipes_sidebar', array( $this, 'delicious_recipes_get_sidebar' ) );

        // Recipe page section hooks.
        add_action( 'delicious_recipes_single_header', array( $this, 'recipe_get_single_header' ) );
        add_action( 'delicious_recipes_single_before_content_start', array( $this, 'recipe_get_gallery' ) );
        add_action( 'delicious_recipes_single_after_the_content', array( $this, 'recipe_main_card' ) );

        // Recipe Main Card hooks.
        add_action( 'delicious_recipes_main_summary', array( $this, 'recipe_main_summary' ) );
        add_action( 'delicious_recipes_ingredients', array( $this, 'recipe_main_ingredients' ) );
        add_action( 'delicious_recipes_instructions', array( $this, 'recipe_main_instructions' ) );
        add_action( 'delicious_recipes_nutrition', array( $this, 'recipe_main_nutrition' ) );
        add_action( 'delicious_recipes_notes', array( $this, 'recipe_main_notes' ) );

        add_action( 'delicious_recipes_single_after_content_end', array( $this, 'recipe_faqs' ), 5 );
        add_action( 'delicious_recipes_single_after_content_end', array( $this, 'recipe_share' ), 10 );
        add_action( 'delicious_recipes_single_after_content_end', array( $this, 'recipe_tags' ), 30 );
        add_action( 'delicious_recipes_single_after_content_end', array( $this, 'recipe_powered_by' ), 40 );
        add_action( 'delicious_recipes_single_after_content_end', array( $this, 'recipe_author' ), 50 );
        add_action( 'delicious_recipes_single_after_content_end', array( $this, 'recipe_navigation' ), 70 );
        add_action( 'delicious_recipes_single_after_content_end', array( $this, 'get_comments' ), 90 );
    }

    /**
     * Recipe archive template hooks.
     *
     * @return void
     */
    private function init_archive_recipe_hooks() {

        // Recipe archive page section hooks.
        add_action( 'delicious_recipes_archive_header', array( $this, 'recipe_archive_header' ) );
        add_action( 'delicious_recipes_before_archive_recipe', array( $this, 'recipe_archive_wrap_start' ) );
        add_action( 'delicious_recipes_after_archive_recipe', array( $this, 'recipe_archive_pagination' ), 10 );
        add_action( 'delicious_recipes_after_archive_recipe', array( $this, 'recipe_archive_wrap_end' ), 20 );
        add_action( 'delicious_recipes_after_archive_recipe', array( $this, 'recipe_all_categories' ), 30 );
        add_action( 'delicious_recipes_after_archive_recipe', array( $this, 'recipes_by_ingredients' ), 40 );
    }

    /**
     * Recipe Search page hooks.
     *
     * @return void
     */
    private function init_recipe_search_hooks() {
        add_action( 'delicious_recipes_search_top_filters', array( $this, 'recipes_search_filters' ) );
    }

    /**
     * Before recipe main content hook.
     *
     * @return void
     */
    public function recipe_primary_wrap_start() {

        if( is_post_type_archive( DELICIOUS_RECIPE_POST_TYPE ) ) :
        ?>
            <div id="primary" class="content-area">
        <?php
        endif;
    }

    /**
     * Before recipe main content hook.
     *
     * @return void
     */
    public function recipe_primary_wrap_end() {

        if( is_post_type_archive( DELICIOUS_RECIPE_POST_TYPE ) ) :
        ?>
            </div>
        <?php
        endif;
    }

    /**
     * Before recipe main content hook.
     *
     * @return void
     */
    public function recipe_main_wrap_start() {
        if( is_singular( DELICIOUS_RECIPE_POST_TYPE ) ) :
        ?>
            <div id="primary" class="content-area">
                <main class="site-main">
        <?php
        endif;

        if( is_post_type_archive( DELICIOUS_RECIPE_POST_TYPE ) ) :
        ?>
            <div class="recipe-archive">
        <?php
        endif;
    }

    /**
     * Before recipe main content hook.
     *
     * @return void
     */
    public function recipe_main_wrap_end() {
        if( is_singular( DELICIOUS_RECIPE_POST_TYPE ) ) :
        ?>
                </main>
            </div>
        <?php
        endif;

        if( is_post_type_archive( DELICIOUS_RECIPE_POST_TYPE ) ) :
        ?>
            </div>
        <?php
        endif;
    }

    /**
     * Get recipe single header.
     *
     * @return void
     */
    public function recipe_get_single_header() {
        delicious_recipes_get_template( 'recipe/header.php' );
    }

    /**
     * Get recipe single gallery.
     *
     * @return void
     */
    public function recipe_get_gallery() {
        delicious_recipes_get_template( 'recipe/gallery.php' );
    }

    /**
     * Get Recipe page main card template.
     *
     * @return void
     */
    public function recipe_main_card() {
        delicious_recipes_get_template( 'recipe/recipe-main.php' );
    }

    /**
     * Get recipe summary template.
     *
     * @return void
     */
    public function recipe_main_summary( $card_layout = "" ) {
        $global_settings    = delicious_recipes_get_global_settings();
        $global_card_layout = isset( $global_settings['defaultCardLayout'] ) && ! empty( $global_settings['defaultCardLayout'] ) ? $global_settings['defaultCardLayout'] : 'default';
        $card_layout        = $card_layout ? $card_layout : $global_card_layout;

        delicious_recipes_get_template_part( 'recipe/recipe-block/summary', $card_layout );
    }

    /**
     * Get recipe nutrition template.
     *
     * @return void
     */
    public function recipe_main_nutrition() {
        delicious_recipes_get_template( 'recipe/recipe-block/nutrition.php' );
    }

    /**
     * Get recipe notes template.
     *
     * @return void
     */
    public function recipe_main_notes() {
        delicious_recipes_get_template( 'recipe/recipe-block/notes.php' );
    }

    /**
     * Get recipe instructions template.
     *
     * @return void
     */
    public function recipe_main_instructions() {
        delicious_recipes_get_template( 'recipe/recipe-block/instructions.php' );
    }

    /**
     * Get recipe ingredients template.
     *
     * @return void
     */
    public function recipe_main_ingredients() {
        delicious_recipes_get_template( 'recipe/recipe-block/ingredients.php' );
    }

    /**
     * Get Recipe FAQs template.
     *
     * @return void
     */
    public function recipe_faqs() {
        delicious_recipes_get_template( 'recipe/recipe-faqs.php' );
    }

    /**
     * Get Recipe page main card template.
     *
     * @return void
     */
    public function recipe_powered_by() {
        delicious_recipes_get_template( 'recipe/powered-by.php' );
    }

    /**
     * Get Recipe page main card template.
     *
     * @return void
     */
    public function recipe_share() {
        delicious_recipes_get_template( 'recipe/recipe-share.php' );
    }

    /**
     * Get Recipe author profile template.
     *
     * @return void
     */
    public function recipe_author() {
        delicious_recipes_get_template( 'recipe/author-profile.php' );
    }

    /**
     * Get Recipe navigation template.
     *
     * @return void
     */
    public function recipe_navigation() {
        delicious_recipes_get_template( 'recipe/navigation.php' );
    }

    /**
     * Get Recipe page main card template.
     *
     * @return void
     */
    public function recipe_tags() {
        delicious_recipes_get_template( 'recipe/recipe-tags.php' );
    }

    /**
     * Get Sidebar.
     *
     * @return void
     */
    public function delicious_recipes_get_sidebar() {
        delicious_recipes_get_template( 'global/sidebar.php' );
    }

    /**
     * Get Comments template.
     *
     * @return void
     */
    public function get_comments() {
        $global_toggles  = delicious_recipes_get_global_toggles_and_labels();
        if ( current_theme_supports( 'delicious-recipes' ) && $global_toggles['enable_comments'] ) {
            comments_template();
        }
    }

    /**
     * Get recipe archive header.
     *
     * @return void
     */
    public function recipe_archive_header() {
        delicious_recipes_get_template( 'archive/header.php' );
    }

    /**
     * Get recipe archive wrap start.
     *
     * @return void
     */
    public function recipe_archive_wrap_start() {
        delicious_recipes_get_template( 'archive/wrap-start.php' );
    }

    /**
     * Get recipe archive pagination.
     *
     * @return void
     */
    public function recipe_archive_pagination() {
        delicious_recipes_get_template( 'archive/pagination.php' );
    }

    /**
     * Get recipe archive wrap end.
     *
     * @return void
     */
    public function recipe_archive_wrap_end() {
        delicious_recipes_get_template( 'archive/wrap-end.php' );
    }

    /**
     * Get all recipe categories.
     *
     * @return void
     */
    public function recipe_all_categories() {
        delicious_recipes_get_template( 'archive/all-categories.php' );
    }

    /**
     * Get all recipe categories .
     *
     * @return void
     */
    public function recipes_by_ingredients() {
        delicious_recipes_get_template( 'archive/recipes-by-ingredients.php' );
    }

    /**
     * Search page top filters template.
     *
     * @return void
     */
    public function recipes_search_filters() {
        delicious_recipes_get_template( 'global/search/top-filters.php' );
    }

}

Delicious_Recipes_Template_Hooks::get_instance();
