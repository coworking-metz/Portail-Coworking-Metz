/* global fp_pyp_product_settings_obj */

jQuery( function ( $ ) {

    // fp_pyp_product_settings_obj is required to continue, ensure the object exists
    if ( typeof fp_pyp_product_settings_obj === 'undefined' ) {
        return false ;
    }

    var pyp_product = {
        /**
         * Initialize PYP Product settings UI events.
         */
        init : function () {

            this.trigger_on_page_load() ;
            this.on_change_product_type = this.on_change_product_type.bind( this ) ;
            this.on_load_product_type = this.on_load_product_type.bind( this ) ;
            this.on_variations_loaded = this.on_variations_loaded.bind( this ) ;

            $( document ).on( 'change' , '#product-type' , this.on_change_product_type ) ;
        } ,
        trigger_on_page_load : function ( ) {

            $( '._checkboxvalue_field' ).hide() ;
            $( '._getminimumprice_field' ).hide() ;
            $( '._hideminimum_field' ).hide() ;
            $( '._getrecommendedprice_field' ).hide() ;
            $( '._getmaximumprice_field' ).hide() ;
            $( '._hidemaximum_field' ).hide() ;

            this.on_load_product_type( $( "#product-type" ).val() ) ;
        } ,
        on_change_product_type : function ( evt ) {
            var $product_type = $( evt.currentTarget ).val() ;

            this.on_load_product_type( $product_type ) ;
        } ,
        on_load_product_type : function ( $product_type ) {
            $product_type = $product_type || '' ;

            if ( $product_type === "variable" ) {
                if ( $( '.woocommerce_variation' ).size() > 0 ) {
                    pyp_product.on_variations_loaded( '' , $( '.woocommerce_variation' ).size() ) ;
                }

                $( '#woocommerce-product-data' ).on( 'woocommerce_variations_loaded' , function ( evt ) {
                    pyp_product.on_variations_loaded( evt ) ;
                } ) ;

                $( document.body ).on( 'woocommerce_variations_added' , function ( evt , qty ) {
                    pyp_product.on_variations_loaded( evt , qty ) ;
                } ) ;
            } else if ( $product_type === "simple" || $product_type === 'subscription' ) {

                $( '._checkboxvalue_field' ).show() ;
                $( '._getminimumprice_field' ).show() ;
                $( '._hideminimum_field' ).show() ;
                $( '._getrecommendedprice_field' ).show() ;
                $( '._getmaximumprice_field' ).show() ;
                $( '._hidemaximum_field' ).show() ;

                load_pyp_product_actions.init() ;
            }
        } ,
        on_variations_loaded : function ( evt , qty ) {
            qty = qty || 0 ;

            var $wrapper = $( "#variable_product_options" ).find( ".woocommerce_variations" ) ,
                    variation_count = parseInt( $wrapper.attr( "data-total" ) , 10 ) + qty ;

            if ( qty > 0 ) {
                variation_count = $( '.woocommerce_variation' ).size() ;
            }

            for ( var i = 0 ; i < variation_count ; i ++ ) {
                ( function ( i ) {
                    load_pyp_variation_actions.init( i ) ;
                } )( i ) ;
            }
        }
    } ;

    var load_pyp_product_actions = {
        /**
         * PYP Product Actions.
         */
        init : function () {

            this.trigger_on_page_load() ;

            $( document ).on( 'change' , '#_input_price_type' , this.toggle_input_price_type ) ;
        } ,
        trigger_on_page_load : function () {

            this.on_load_pyp_html_fields( $( "#_input_price_type" ).val() ) ;
        } ,
        toggle_input_price_type : function ( evt ) {
            var $type = $( evt.currentTarget ).val() ;

            load_pyp_product_actions.on_load_pyp_html_fields( $type ) ;
        } ,
        on_load_pyp_html_fields : function ( $type ) {
            $type = $type || '' ;

            $( "._input_amount_for_pay_field" ).show() ;
            $( "._getminimumprice_field" ).hide() ;
            $( "._getmaximumprice_field" ).hide() ;
            $( "._getrecommendedprice_field" ).hide() ;
            $( "._hidemaximum_field" ).hide() ;
            $( "._hideminimum_field" ).hide() ;

            if ( $type === 'number_input' || $type === '' ||  $type === 'text_input') {
                $( "._input_amount_for_pay_field" ).hide() ;
                $( "._getminimumprice_field" ).show() ;
                $( "._getmaximumprice_field" ).show() ;
                $( "._getrecommendedprice_field" ).show() ;
                $( "._hidemaximum_field" ).show() ;
                $( "._hideminimum_field" ).show() ;
            }
        }
    } ;

    var load_pyp_variation_actions = {
        /**
         * PYP Variation Actions.
         */
        init : function ( variation_row_index ) {
            this.variation_row_index = variation_row_index ;
            this.trigger_on_page_load( this.variation_row_index ) ;
        } ,
        trigger_on_page_load : function ( i ) {

            this.on_load_variation_pyp_html_fields( i , $( 'select[name="_input_type[' + i + ']"]' ).val() ) ;
            this.toggle_variation_input_price_type( i ) ;
        } ,
        toggle_variation_input_price_type : function ( i ) {

            $( document ).on( 'change' , 'select[name="_input_type[' + i + ']"]' , function () {
                var $type = $( this ).val() ;

                load_pyp_variation_actions.on_load_variation_pyp_html_fields( i , $type ) ;
            } ) ;
        } ,
        on_load_variation_pyp_html_fields : function ( i , $type ) {
            $type = $type || '' ;

            $( '[name="_input_for_pay[' + i + ']"]' ).parent().show() ;
            $( 'select[name="_hideminimumprice[' + i + ']"]' ).parent().hide() ;
            $( '[name="_maximumprice[' + i + ']"]' ).parent().hide() ;
            $( '[name="_recommendedprice[' + i + ']"]' ).parent().hide() ;
            $( 'select[name="_hidemaximumprice[' + i + ']"]' ).parent().hide() ;
            $( '[name="_minimumprice[' + i + ']"]' ).parent().hide() ;

            if ( $type === 'number_input' || $type === '' ||  $type === 'text_input') {
                $( '[name="_input_for_pay[' + i + ']"]' ).parent().hide() ;
                $( 'select[name="_hideminimumprice[' + i + ']"]' ).parent().show() ;
                $( '[name="_maximumprice[' + i + ']"]' ).parent().show() ;
                $( '[name="_recommendedprice[' + i + ']"]' ).parent().show() ;
                $( 'select[name="_hidemaximumprice[' + i + ']"]' ).parent().show() ;
                $( '[name="_minimumprice[' + i + ']"]' ).parent().show() ;
            }
        }
    } ;

    pyp_product.init() ;

} ) ;