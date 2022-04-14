<?php
/**
 * Filter by Order.
 */
$sorting_options = array(
    ""           => __( "Sorting", 'delicious-recipes' ),
    "title_asc"  => __("Title (A-Z)", 'delicious-recipes'),
    "title_desc" => __("Title (Z-A)", 'delicious-recipes'),
    "date_desc"  => __("Date (Newest)", 'delicious-recipes'),
    "date_asc"   => __("Date (Oldest)", 'delicious-recipes'),
);
?>
<select class="js-select2"  name='sorting'>
    <?php foreach( $sorting_options as $key => $value ) : ?>
        <option value="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( sanitize_title( $value ) ); ?>" name='sorting'>
            <?php echo esc_html( $value ); ?>
        </option>
    <?php endforeach; ?>
</select>
<?php
