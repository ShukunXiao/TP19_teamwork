<?php
/**
 * Recipe Archive Wrap Start
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipe/archive/wrap-start.php.
 *
 * HOWEVER, on occasion WP Delicious will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://wpdelicious.com/docs/template-structure/
 * @package     Delicious_Recipes/Templates
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<main id="main" class="site-main">
    <div class="dr-archive-list-wrapper">
        <div class="dr-archive-list-gridwrap" itemscope itemtype="http://schema.org/ItemList">

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */