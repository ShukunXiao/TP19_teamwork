<?php
/**
 * Recipe Categories Dropdown
 *
 * This template can be overridden by copying it to yourtheme/delicious-recipe/widgets/categories-dropdown.php.
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

$ran         = rand(1,1000); $ran++;
$dropdown_id = "dr-category-dropdown-{$ran}";
$type_attr   = current_theme_supports( 'html5', 'script' ) ? '' : ' type="text/javascript"';

?>
<label class="screen-reader-text" for="<?php echo esc_attr( $dropdown_id ); ?>"><?php echo esc_html( $title ); ?></label>

<select name="dr-recipe-cat-dropdown" id="<?php echo esc_attr( $dropdown_id ); ?>">
    <option value=""><?php echo esc_html__( 'Select Category', 'delicious-recipes' ); ?></option>
    <?php foreach ( $categories as $key => $value ) {

        $category = get_term( $value, $taxonomy );

        if( empty( $category ) && is_wp_error( $category ) ) {
            return;
        } ?>

        <option value="<?php echo esc_url( get_term_link( $category->term_id ) ); ?>">
            <?php echo esc_html( $category->name ); ?>
            <?php if( $show_counts ) {
                echo sprintf( '(%1$s)', esc_html( $category->count ) ); 
            } ?>
        </option>

    <?php } ?>
</select>

<script<?php echo $type_attr; ?>>
/* <![CDATA[ */
(function() {
	var dropdown = document.getElementById( "<?php echo esc_js( $dropdown_id ); ?>" );
	function onSelectChange() {
		if ( dropdown.options[ dropdown.selectedIndex ].value !== '' ) {
			document.location.href = this.options[ this.selectedIndex ].value;
		}
	}
	dropdown.onchange = onSelectChange;
})();
/* ]]> */
</script>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */