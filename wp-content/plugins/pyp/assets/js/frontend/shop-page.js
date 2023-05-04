/* global fp_pyp_shop_page_obj */

jQuery( function ( $ ) {
    // fp_pyp_shop_page_obj is required to continue, ensure the object exists
    if ( typeof fp_pyp_shop_page_obj === 'undefined' ) {
        return false ;
    }


    $( '.add_to_cart_button' ).each( function () {
        var $product_id = $( this ).attr( 'data-product_id' ) ;
        var is_pyp_product = 'no' ;

        if ( $product_id === fp_pyp_shop_page_obj.product_id ) {
            if ( fp_pyp_shop_page_obj.is_pyp_product ) {
                $( this ).attr( 'href' , fp_pyp_shop_page_obj.product_url ) ;
                $( this ).removeClass( 'product_type_simple' ) ;
                is_pyp_product = 'yes' ;
            }

            $( this ).attr( 'data-checkbox' , is_pyp_product ) ;
            $( this ).attr( 'data-redirect' , fp_pyp_shop_page_obj.product_url ) ;
        }
    } ) ;
} ) ;