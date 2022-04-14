<?php
/**
 * Browse Recipes Template.
 */
?>
<div class="dr-archive-list-wrapper" id="browse">
    <header class="dr-archive-header">
        <h2 class="dr-archive-title"><?php esc_html_e( 'Browse Recipes', 'delicious-recipes' ); ?></h2>
    </header>
    <?php 
        $data = array(
            'dashboard_page'  => true
        ); 
        delicious_recipes_get_template( 'global/searchpage-content.php', $data ); 
    ?>
</div>
<?php
