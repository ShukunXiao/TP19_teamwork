<?php
/**
 * Recipe keys additional taxonomy metas.
 * 
 * @package Delicious_Recipes
 */
$tax_color = $taxonomy === 'recipe-badge' ? '#E84E3B' : '';
?>
<div class="form-field">
    <label for="dr_taxonomy_metas[taxonomy_color]"><?php _e( 'Taxonomy Color', 'delicious-recipes' ); ?></label>
    <input class="dr-colorpickr" type="text" name="dr_taxonomy_metas[taxonomy_color]" id="dr_taxonomy_metas[taxonomy_color]" value="<?php echo esc_attr( $tax_color ) ?>">
    <p class="description"><?php _e( 'Choose color for the taxonomy.','delicious-recipes' ); ?></p>
</div>
<div class="form-field term-group">
    <label for="dr_taxonomy_metas[taxonomy_image]"><?php _e('Taxonomy Image', 'delicious-recipes'); ?></label>
        <input type="hidden" id="dr_taxonomy_metas[taxonomy_image]" name="dr_taxonomy_metas[taxonomy_image]" class="dr_tax_image_media_id" value="">
    <div id="dr-tax-image-wrapper"></div>
    <p>
        <input type="button" class="button button-secondary dr_tax_add_media_button" id="dr_tax_add_media_button" name="dr_tax_add_media_button" value="<?php _e( 'Add Image', 'delicious-recipes' ); ?>" />
        <input type="button" class="button button-secondary dr_tax_remove_media_remove" id="dr_tax_remove_media_remove" name="dr_tax_remove_media_remove" value="<?php _e( 'Remove Image', 'delicious-recipes' ); ?>" />
    </p>
</div>
<div class="form-field">
    <label for="dr_taxonomy_metas[taxonomy_svg]"><?php _e( 'Taxonomy SVG Icon', 'delicious-recipes' ); ?></label>
    <span class="dr-icon-holder"></span>
    <input class="taxonomy_svg" type="text" name="dr_taxonomy_metas[taxonomy_svg]" id="dr_taxonomy_metas[taxonomy_svg]" value="" autocomplete="off"/>
    <div class="dr-recipe-icons-wrap">
        <ul class="dr-tab-titles">
            <li class="active-tab"><?php esc_html_e( 'Recipe Icons' , 'delicious-recipes' ); ?></li>
            <li><?php esc_html_e( 'FontAwesome Icons', 'delicious-recipes' ); ?></li>
            <li><?php esc_html_e( 'PNG Icons', 'delicious-recipes' ); ?></li>
        </ul>

        <div class="dr-tabs-content">
            <div class="dr-tab-content-inn">
                <input class="dr-adm-ico-search adm-ico-search" type="text" placeholder="Search here..." value="">
                <?php 
                    $icons = Delicious_Recipes_SVG::$recipe_keys_icons;
                    if ( $icons ) {
                        echo '<ul class="dr-tab-icon-lists">';
                            foreach( $icons as $icon => $svg ){
                                echo '<li class="'. esc_attr( $icon ) .'">' . $svg . '</li>';
                            }
                        echo '</ul>';
                    }              
                ?>
            </div>
            <div class="dr-tab-content-inn">
                <input class="dr-adm-ico-search fa-icon-search" type="text" placeholder="Search here..." value="">
                <?php
                    $fontawesome_icons = delicious_recipes_get_fontawesome_icons();
                    if ( ! empty( $fontawesome_icons ) ) {
                        echo '<ul class="dr-tab-icon-lists">';
                            foreach( $fontawesome_icons as $key => $icon ) {
                                echo '<li><i class="' . esc_attr( $icon ) . '"></i></li>';
                            }
                        echo '</ul>';
                    }
                ?>
            </div>
            <div class="dr-tab-content-inn">
                <input class="dr-adm-ico-search adm-png-search" type="text" placeholder="Search here..." value="">
                <?php
                    $png_icons = delicious_recipes_get_png_icons();
                    if ( ! empty( $png_icons ) ) {
                        echo '<ul class="dr-tab-icon-lists">';
                            foreach( $png_icons as $key => $icon ) {
                                echo '<li class="' . esc_attr( $key ) . '"><img src="' . esc_url( $icon ) . '"/></li>';
                            }
                        echo '</ul>';
                    }
                ?>
            </div>
        </div>
    </div>
    <p class="description"><?php _e( 'Choose svg icon for the taxonomy.','delicious-recipes' ); ?></p>
</div>
