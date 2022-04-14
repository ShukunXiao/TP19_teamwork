<?php
/**
 * Search page template
 * 
 * @package Delicious_Recipes
 */

/**
 * Get page header.
 */
get_header();
?>
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <?php
                /**
                * Recipe content template load.
                */
                delicious_recipes_get_template( 'global/searchpage-content.php' );
            ?>
        </main>
    </div>
<?php
/**
 * Get sidebar.
 */
get_sidebar();
/**
 * Get footer.
 */
get_footer();
