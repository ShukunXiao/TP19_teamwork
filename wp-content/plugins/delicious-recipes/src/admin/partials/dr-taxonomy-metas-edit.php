<?php
/**
 * Edit taxonomy metas
 */
$tid               = $taxonomy->term_id;
$tax               = $taxonomy->taxonomy;
$dr_taxonomy_metas = get_term_meta( $tid, 'dr_taxonomy_metas', true );
$tax_color         = isset( $dr_taxonomy_metas['taxonomy_color'] ) ? $dr_taxonomy_metas['taxonomy_color'] : ( $tax === "recipe-badge" ? '#E84E3B' : '' );
$tax_image         = isset( $dr_taxonomy_metas['taxonomy_image'] ) ? $dr_taxonomy_metas['taxonomy_image'] : false;
$tax_svg           = isset( $dr_taxonomy_metas['taxonomy_svg'] ) ? $dr_taxonomy_metas['taxonomy_svg'] : '';
?>
<tr class="form-field term-tax-color-wrap">
    <th scope="row"><label for="tax-color"><?php _e( 'Taxonomy Color', 'delicious-recipes' ); ?></label></th>
    <td><input class="dr-colorpickr" type="text" name="dr_taxonomy_metas[taxonomy_color]" id="dr_taxonomy_metas[taxonomy_color]" value="<?php echo esc_attr( $tax_color ) ?>"/>
    <p class="description"><?php _e( 'Choose color for the taxonomy.','delicious-recipes' ); ?></p></td>
</tr>

<tr class="form-field term-tax-image-wrap">
    <th scope="row"><label for="tax-image"><?php _e( 'Taxonomy Image', 'delicious-recipes' ); ?></label></th>
    <td>
        <input type="hidden" id="dr_taxonomy_metas[taxonomy_image]" name="dr_taxonomy_metas[taxonomy_image]" class="dr_tax_image_media_id" value="<?php echo esc_attr( $tax_image ); ?>">
        <div id="dr-tax-image-wrapper">
            <?php 
                if( $tax_image ) {
                    $image_thumb = wp_get_attachment_image( $tax_image, 'thumbnail' );
                    echo wp_kses_post( $image_thumb );
                }
            ?>
        </div>
        <input type="button" class="button button-secondary dr_tax_add_media_button" id="dr_tax_add_media_button" name="dr_tax_add_media_button" value="<?php _e( 'Add/Replace Image', 'delicious-recipes' ); ?>" />
        <input type="button" class="button button-secondary dr_tax_remove_media_remove" id="dr_tax_remove_media_remove" name="dr_tax_remove_media_remove" value="<?php _e( 'Remove Image', 'delicious-recipes' ); ?>" />
        <p class="description"><?php _e( 'Choose image for the taxonomy.','delicious-recipes' ); ?></p>
    </td>
</tr>

<tr class="form-field term-tax-svg-wrap">
    <th scope="row"><label for="tax-svg"><?php _e( 'Taxonomy SVG Icon', 'delicious-recipes' ); ?></label></th>
    <td>
        <span class="dr-icon-holder"><?php 
            if( $tax_svg ) {
                $svg       = delicious_recipes_get_svg( $tax_svg, 'recipe-keys', '#000000' );
                $png_array = delicious_recipes_get_png_icons();
                $png       = isset( $png_array[$tax_svg] ) ? $png_array[$tax_svg] : false;
                if( $svg ) {
                    echo $svg; 
                } elseif( $png ) {
                    echo '<img src="' . esc_url( $png ) . '" />';
                } else {
                    echo '<i class="' . esc_attr( $tax_svg ) . '"></i>';
                }
            }
        ?></span>
        <input class="taxonomy_svg" type="text" name="dr_taxonomy_metas[taxonomy_svg]" id="dr_taxonomy_metas[taxonomy_svg]" value="<?php echo esc_attr( $tax_svg ) ?>" autocomplete="off"/>
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
                                    $class = $tax_svg === $icon ? ' dr-selected-icon' : '';
                                    echo '<li class="'. esc_attr( $icon ) . esc_attr( $class ) .'">' . $svg . '</li>';
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
                                    $class = $tax_svg === $icon ? 'dr-selected-icon' : '';
                                    echo '<li class="' . esc_attr( $class ) .'"><i class="' . esc_attr( $icon ) . '"></i></li>';
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
    </td>
</tr>
