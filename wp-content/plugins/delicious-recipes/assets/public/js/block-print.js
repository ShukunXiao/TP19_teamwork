( function( $, delrecipesRecipeCard ) {
    'use scrict';

    function dr_block_print_recipe( recipeID, servings, blockType, blockId ) {
        servings = servings || 0;
        blockType = blockType || 'recipe-card';
        blockId = blockId || 'delicious-recipes-pro-recipe-card';
    
        const urlParts = delrecipesRecipeCard.homeURL.split( /\?(.+)/ );
        let printUrl = urlParts[ 0 ];
    
        if ( delrecipesRecipeCard.permalinks ) {
            printUrl += 'delrecipes_block_print/' + recipeID + '/';
    
            if ( urlParts[ 1 ] ) {
                printUrl += '?' + urlParts[ 1 ];
                printUrl += '&block-type=' + blockType;
                printUrl += '&block-id=' + blockId;
    
                if ( servings ) {
                    printUrl += '&servings=' + servings;
                }
            } else {
                printUrl += '?block-type=' + blockType;
                printUrl += '&block-id=' + blockId;
    
                if ( servings ) {
                    printUrl += '&servings=' + servings;
                }
            }
        } else {
            printUrl += '?delrecipes_block_print=' + recipeID;
            printUrl += '&block-type=' + blockType;
            printUrl += '&block-id=' + blockId;
    
            if ( servings ) {
                printUrl += '&servings=' + servings;
            }
    
            if ( urlParts[ 1 ] ) {
                printUrl += '&' + urlParts[ 1 ];
            }
        }

        const print_window = window.open( printUrl, '_blank' );
        print_window.delrecipesRecipeCard = delrecipesRecipeCard;
        print_window.onload = function() {
            print_window.focus();
            print_window.document.title = document.title;
            print_window.history.pushState( '', 'Print Recipe', location.href.replace( location.hash, '' ) );
    
            setTimeout( function() {
                print_window.print();
            }, 500 );
    
            print_window.onfocus = function() {
                setTimeout( function() {
                    print_window.close();
                }, 500 );
            };
        };
    }

    $( document ).ready( function() {

        const servings_size = $( document ).find( '.dr-buttons.dr-recipe-card-block-print .dr-print-trigger' ).data( 'servings-size' );

        if ( servings_size ) {
            $( document ).find( '.dr-buttons.dr-recipe-buttons-block .dr-print-trigger' ).attr( 'data-servings-size', servings_size );
        }

        $( '.dr-recipe-card-block-print .dr-print-trigger, .dr-recipe-buttons-block .dr-print-trigger' ).each( function() {
            const $printBtn = $( this );
    
            $printBtn.on( 'click', function( e ) {
                const $this = $( this );
                const recipeID = $this.data( 'recipe-id' );
                const servings = $this.data( 'servings-size' );
    
                const isRecipeCardBlock  = $this.parents( '.wp-block-delicious-recipes-block-recipe-card' ).length;
                const hasRecipeCardBlock = $( document ).find( '.wp-block-delicious-recipes-block-recipe-card' ).length;
                // const isRecipeButton    = $this.parent().hasClass( '.dr-buttons.dr-recipe-buttons-block' );
    
                let blockType;
                let blockId;
    
                if ( isRecipeCardBlock ) {
                    blockType = 'recipe-card';
                    blockId = $this.parents( '.wp-block-delicious-recipes-block-recipe-card' ).attr( 'id' );
                } else {                        
                    //RecipeButton
                    blockType = 'recipe-card';
                    blockId = $this.attr( 'href' ).substr( 1, $this.attr( 'href' ).length );
                }
    
                if ( recipeID && hasRecipeCardBlock ) {
                    e.preventDefault();
                    dr_block_print_recipe( recipeID, servings, blockType, blockId );
                }
            } );
        } );
    } );
}( jQuery, delrecipesRecipeCard ) );
