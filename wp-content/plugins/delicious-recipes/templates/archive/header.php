<?php
/**
 * Recipe Archive page header.
 * 
 * @package Delicious_Recipes
 */
$global_settings   = delicious_recipes_get_global_settings();
$showArchiveHeader = isset( $global_settings['enableArchiveHeader']['0'] ) && 'yes' === $global_settings['enableArchiveHeader']['0'] ? true : false;

if ( $showArchiveHeader ) :
    ?>
        <header class="dr-entry-header">
            <?php 
                the_archive_title( '<h1 class="dr-entry-title" itemprop="name">', '</h1>' ); 
                the_archive_description( '<div class="dr-info" itemprop="description">', '</div>' );
            ?>
        </header>
    <?php 
endif;
