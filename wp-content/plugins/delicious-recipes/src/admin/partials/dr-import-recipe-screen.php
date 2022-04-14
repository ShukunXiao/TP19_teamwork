<div id="delicious-recipe-import">
    <div class="delicious-recipe-outer">
        <header class="dr-header">
            <div class="dr-left-block">
                <h1 class="dr-plugin-title">
                    <img src="<?php echo esc_url( plugin_dir_url( DELICIOUS_RECIPES_PLUGIN_FILE ) ); ?>/assets/images/plugin-logo.png" alt="import recipes">
                </h1>
                <span class="dr-page-name">
                <?php esc_html_e( 'Import Recipes', 'delicious-recipes' ); ?>
                </span>
            </div>
        </header>
        <div class="dr-tab-main dr-vertical-tab">
            <div class="dr-tab-wrap">
                <div class="dr-tab-wrap-inner">
                <?php 
                $i = 1;
                    foreach( self::$importers as $importer ) { ?>
                        <a class="dr-tab dr-<?php echo sanitize_title( $importer->get_name() ); ?> <?php echo $i === 1 ? 'current' : ''; ?>"><?php echo esc_html( $importer->get_name() ); ?></a>
                    <?php 
                $i++;
                } ?>
                </div>
            </div>
            <div class="dr-tab-content-wrap">
                <?php
                    $i = 1; 
                    foreach ( self::$importers as $importer ) { ?>
                    <div class="dr-tab-content dr-<?php echo sanitize_title( $importer->get_name() ); ?>-content <?php echo $i === 1 ? 'current' : ''; ?>">
                        <div class="dr-form-block">
                            <div class="dr-title-wrap">
                                <h3 class="dr-title"><?php 
                                    /* translators: %1$s: importer plugin name */
                                    printf( __( 'Import recipes from %1$s', 'delicious-recipes' ), $importer->get_name() ) 
                                ?></h3>
                            </div>
                                <div class="dr-block-content">
                                    <?php
                                        if ( ! $importer->is_plugin_active() ) {
                                            /* translators: %1$s: importer plugin name %2$s: importer plugin name*/
                                            echo '<h4>' . sprintf( esc_html__( '%1$s plugin is not installed or activated. Please activate the %2$s plugin first to start import.', 'delicious-recipes' ), esc_html( $importer->get_name() ), esc_html( $importer->get_name() ) ) . '</h4>';
                                        } else {
                                                $recipes_to_import = array();
                                                $recipe_count = $importer->get_recipe_count();

                                                if ( intval( $recipe_count ) > 0 ) {
                                                    $recipes_to_import[ $importer->get_uid() ] = array(
                                                        'name'     => $importer->get_name(),
                                                        'count'    => $recipe_count,
                                                        'recipes'  => $importer->get_recipes(),
                                                        'settings' => $importer->get_settings_html(),
                                                    );
                                                }

                                            if ( 0 === count( $recipes_to_import ) ) :
                                                /* translators: %1$s: importer plugin name %2$s: importer plugin name*/
                                                echo '<h4>' . sprintf( esc_html__( 'All the recipes from %1$s plugin has been imported. You can now deactivate the %2$s plugin.', 'delicious-recipes' ), esc_html( $importer->get_name() ), esc_html( $importer->get_name() ) ) . '</h4>';
                                            else :
                                                foreach ( $recipes_to_import as $uid => $importer ) : ?>
                                                    <?php if ( intval( $importer['count'] ) > 0 ) :
                                                        if ( is_int( $importer['count'] ) ) {
                                                            /* translators: %d: recipes count */
                                                            printf( esc_html( _n( '%d recipe found', '%d recipes found', $importer['count'], 'delicious-recipes' ) ), intval( $importer['count'] ) );
                                                        } else {
                                                            echo esc_html( $importer['count'] ) . ' ' . esc_html__( ' recipes found', 'delicious-recipes' );
                                                        }
                                                    endif; // Recipe count. ?>

                                                    <form method="post" name="dr-recipe-import" action="<?php echo admin_url('admin-ajax.php');?>">
                                                        <input type="hidden" name="action" value="dr_import_recipes">
                                                        <input type="hidden" name="dr_recipe_importer" value="<?php echo esc_attr( $uid ); ?>">
                                                        
                                                        <?php 
                                                            wp_nonce_field( 'dr_import_recipes', 'dr_import_recipes', false );
                                                            $settings = apply_filters( 'delicious_recipes_import_settings_' . $uid, $importer['settings'] );

                                                            if ( $settings ) : ?>
                                                                <?php echo $settings; ?>
                                                            <?php endif; // Settings. 
                                                        ?>

                                                        <h3><?php esc_html_e( 'Recipes to Import', 'delicious-recipes' ); ?></h3>
                                                        <table id="<?php echo esc_attr( $uid ) ?>-dr-importRecipe" class="widefat fixed stripe dataTable dr-importRecipe" cellspacing="0">
                                                            <thead>
                                                                <tr>
                                                                    <th id="recipe-cb" class="manage-column column-cb check-column" scope="col"><input id="dr-select-all" type="checkbox" /></th>
                                                                    <th id="recipe-title" class="manage-column column-recipe-title" scope="col"><b><?php _e( 'Recipe Title', 'delicious-recipes' ) ?></b></th>
                                                                    <th id="recipe-id" class="manage-column column-recipe-id" scope="col"><b><?php _e( 'Recipe ID', 'delicious-recipes' ) ?></b></th>
                                                                    <th id="recipe-author" class="manage-column column-recipe-author" scope="col"><b><?php _e( 'Author', 'delicious-recipes' ) ?></b></th>
                                                                    <th id="recipe-published" class="manage-column column-recipe-published" scope="col"><b><?php _e( 'Date Published', 'delicious-recipes' ) ?></b></th>
                                                                    <th id="recipe-image" class="manage-column column-recipe-image" scope="col"><b><?php _e( 'Featured Image', 'delicious-recipes' ) ?></b></th>
                                                                </tr>
                                                            </thead>
                                                            <tfoot>
                                                                <tr>
                                                                    <th id="recipe-cb" class="manage-column column-cb check-column" scope="col"><input id="dr-select-all" type="checkbox" /></th>
                                                                    <th id="recipe-title" class="manage-column column-recipe-title" scope="col"><b><?php _e( 'Recipe Title', 'delicious-recipes' ) ?></b></th>
                                                                    <th id="recipe-id" class="manage-column column-recipe-id" scope="col"><b><?php _e( 'Recipe ID', 'delicious-recipes' ) ?></b></th>
                                                                    <th id="recipe-author" class="manage-column column-recipe-author" scope="col"><b><?php _e( 'Author', 'delicious-recipes' ) ?></b></th>
                                                                    <th id="recipe-published" class="manage-column column-recipe-published" scope="col"><b><?php _e( 'Date Published', 'delicious-recipes' ) ?></b></th>
                                                                    <th id="recipe-image" class="manage-column column-recipe-image" scope="col"><b><?php _e( 'Featured Image', 'delicious-recipes' ) ?></b></th>
                                                                </tr>
                                                            </tfoot>
                                                            <tbody>
                                                                <?php
                                                                    $recipes = $importer['recipes'];
                                                                    $i = 1;
                                                                    foreach ( $recipes as $id => $recipe ) :
                                                                ?>
                                                                    <tr id="<?php echo esc_attr( 'dr_' . $uid . '_' . $id ) ?>" class="<?php echo $i%2 === 0 ? 'alternate' : ''; ?>" valign="top">
                                                                        <th class="check-column" scope="row"><input id="<?php echo esc_attr( sanitize_title( $recipe['name'] ) . $id ) ?>" type="checkbox" name="recipes[]" value="<?php echo esc_attr( $id ); ?>" /></th>
                                                                        <td class="column-columnname">
                                                                            <label for="<?php echo esc_attr( sanitize_title( $recipe['name'] ) . $id ) ?>"><?php echo esc_html( $recipe['name'] ); ?></label>
                                                                            <div class="row-actions">
                                                                                <?php if ( $recipe['url'] ) : ?>
                                                                                    <span><a target="_blank" href="<?php echo esc_url( $recipe['url'] ); ?>"><?php _e( 'Edit', 'delicious-recipes' ); ?></a> |</span>
                                                                                    <span><a target="_blank" href="<?php echo esc_url( $recipe['view'] ); ?>"><?php _e( 'View', 'delicious-recipes' ) ?></a></span>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        </td>
                                                                        <td><?php echo esc_html( $id ); ?></td>
                                                                        <td class="column-columnname">
                                                                            <?php 
                                                                                if ( $recipe['author'] ) {
                                                                                    echo esc_html( $recipe['author'] );
                                                                                }
                                                                            ?>
                                                                        </td>
                                                                        <td class="column-columnname">
                                                                            <?php 
                                                                                if ( $recipe['date'] ) {
                                                                                    echo esc_html( $recipe['date'] );
                                                                                }
                                                                            ?>
                                                                        </td>
                                                                        <td class="column-columnname">
                                                                            <?php 
                                                                                if ( $recipe['image'] ) {
                                                                                    echo '<img src="'. esc_url( $recipe['image'] ) .'" height="100" width="100" />';
                                                                                }
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php
                                                                    $i++;
                                                                    endforeach;
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                        <div class="dr-field dr-submit">
                                                            <?php submit_button( __( 'Import Selected Recipes / Taxonomies', 'delicious-recipes' ), 'primary', 'submit', true, ['style' => 'float:right'] ); ?>
                                                        </div>
                                                    </form>
                                                <?php endforeach; // Each importer.
                                            endif; // Recipes to import. 
                                        }
                                    ?>
                            </div>
                        </div>
                    </div>
                <?php $i++; } ?>
            </div>
        </div>
    </div>
</div>
<?php
