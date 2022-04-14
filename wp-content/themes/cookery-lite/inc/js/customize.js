jQuery(document).ready(function($) {

    wp.customize.section( 'sidebar-widgets-promo' ).panel( 'frontpage_settings' );
    wp.customize.section( 'sidebar-widgets-promo' ).priority( '20' );
    wp.customize.section( 'sidebar-widgets-about' ).panel( 'frontpage_settings' );
    wp.customize.section( 'sidebar-widgets-about' ).priority( '25' );
    wp.customize.section( 'sidebar-widgets-client' ).panel( 'frontpage_settings' );
    wp.customize.section( 'sidebar-widgets-client' ).priority( '75' );     

    //Scroll to front page section
    $('body').on('click', '#sub-accordion-panel-frontpage_settings .control-subsection .accordion-section-title', function(event) {
        var section_id = $(this).parent('.control-subsection').attr('id');
        scrollToSection( section_id );
    });  
});

function scrollToSection( section_id ){
    var preview_section_id = "banner_section";

    var $contents = jQuery('#customize-preview iframe').contents();

    switch ( section_id ) {

        case 'accordion-section-sidebar-widgets-promo':
        preview_section_id = "promo_section";
        break;

        case 'accordion-section-sidebar-widgets-about':
        preview_section_id = "about_section";
        break;

        case 'accordion-section-featured_recipe_settings':
        preview_section_id = "featured_recipe_section";
        break; 
        
        case 'accordion-section-sidebar-widgets-client':
        preview_section_id = "client_section";
        break;

        case 'accordion-section-newsletter_section':
        preview_section_id = "footer_newsletter_section";
        break;
        
        case 'accordion-section-sidebar-widgets-instagram':
        preview_section_id = "instagram_section";
        break;
        
        case 'accordion-section-front_sort':
        preview_section_id = "banner_section";
        break;
    }

    if( $contents.find('#'+preview_section_id).length > 0 && $contents.find('.home').length > 0 ){
        $contents.find("html, body").animate({
        scrollTop: $contents.find( "#" + preview_section_id ).offset().top
        }, 1000);
    }
}

( function( api ) {

    // Extends our custom "example-1" section.
    api.sectionConstructor['cookery-lite-pro-section'] = api.Section.extend( {

        // No events for this type of section.
        attachEvents: function () {},

        // Always make the section active.
        isContextuallyActive: function () {
            return true;
        }
    } );

} )( wp.customize );