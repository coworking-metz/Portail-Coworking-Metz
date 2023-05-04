<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly
}

if ( ! class_exists( 'FP_PYP_Load_Ajax' ) ):

    /**
     * Handle PYP Ajax Event.
     * 
     * @class FP_PYP_Load_Ajax
     * @category Class
     */
    class FP_PYP_Load_Ajax {

        /**
         * Init FP_PYP_Load_Ajax.
         */
        public static function init() {
            //Get Ajax Events.
            $ajax_events = array (
                'updatedpreviousproductvalue' => true ,
                'optimizeupdatedpreviousproduct' => false
                    ) ;

            foreach ( $ajax_events as $ajax_event => $nopriv ) {
                add_action( 'wp_ajax_fp_pyp_' . $ajax_event , array ( __CLASS__ , $ajax_event ) ) ;

                if ( $nopriv ) {
                    add_action( 'wp_ajax_nopriv_fp_pyp_' . $ajax_event , array ( __CLASS__ , $ajax_event ) ) ;
                }
            }
        }

        public static function updatedpreviousproductvalue() {
            if ( isset( $_POST[ 'proceedanyway' ] ) ) {
                if ( $_POST[ 'proceedanyway' ] == '1' ) {
                    if ( $_POST[ 'whichproduct' ] == '1' ) {
                        $args = array ( 'post_type' => 'product' , 'posts_per_page' => '-1' , 'post_status' => 'publish' , 'fields' => 'ids' , 'cache_results' => false ) ;
                        $products = get_posts( $args ) ;
                        echo json_encode( $products ) ;
                    } else {
                        if ( ! is_array( $_POST[ 'selectedproducts' ] ) ) {
                            $_POST[ 'selectedproducts' ] = ( array ) explode( ',' , $_POST[ 'selectedproducts' ] ) ;
                        }
                        if ( is_array( $_POST[ 'selectedproducts' ] ) ) {
                            foreach ( $_POST[ 'selectedproducts' ]as $particularpost ) {
                                $checkprod = sumo_pyp_get_product( $particularpost ) ;
                                if ( $checkprod->is_type( 'simple' ) || ($checkprod->is_type( 'subscription' )) ) {
                                    if ( $_POST[ 'enableyourprice' ] == 'yes' ) {
                                        update_post_meta( $particularpost , '_checkboxvalue' , 'yes' ) ;
                                    } else {
                                        update_post_meta( $particularpost , '_checkboxvalue' , 'no' ) ;
                                    }
                                    update_post_meta( $particularpost , '_getminimumprice' , $_POST[ 'minimumprice' ] ) ;
                                    update_post_meta( $particularpost , '_hideminimum' , $_POST[ 'hideminimumprice' ] ) ;
                                    update_post_meta( $particularpost , '_getrecommendedprice' , $_POST[ 'recommendedprice' ] ) ;
                                    update_post_meta( $particularpost , '_getmaximumprice' , $_POST[ 'maximumprice' ] ) ;
                                    update_post_meta( $particularpost , '_hidemaximum' , $_POST[ 'hidemaximumprice' ] ) ;
                                } else {
                                    $checkprod = sumo_pyp_get_product( $particularpost ) ;
                                    $hideminprice = $_POST[ 'hideminimumprice' ] ;
                                    $hideminprice = $hideminprice == 'yes' ? '2' : '1' ;

                                    $hidemaxprice = $_POST[ 'hidemaximumprice' ] ;
                                    $hidemaxprice = $hidemaxprice == 'yes' ? '2' : '1' ;
                                    if ( $_POST[ 'enableyourprice' ] == 'yes' ) {
                                        update_post_meta( sumo_pyp_get_product_id($checkprod) , '_selectpayyourprice' , 'two' ) ;
                                    } else {
                                        update_post_meta( sumo_pyp_get_product_id($checkprod) , '_selectpayyourprice' , 'three' ) ;
                                    }
                                    update_post_meta( sumo_pyp_get_product_id($checkprod) , '_minimumprice' , $_POST[ 'minimumprice' ] ) ;
                                    update_post_meta( sumo_pyp_get_product_id($checkprod) , '_hideminimumprice' , $hideminprice ) ;
                                    update_post_meta( sumo_pyp_get_product_id($checkprod) , '_recommendedprice' , $_POST[ 'recommendedprice' ] ) ;
                                    update_post_meta( sumo_pyp_get_product_id($checkprod) , '_maximumprice' , $_POST[ 'maximumprice' ] ) ;
                                    update_post_meta( sumo_pyp_get_product_id($checkprod) , '_hidemaximumprice' , $hidemaxprice ) ;
                                }
                            }
                        }
                        echo json_encode( "success" ) ;
                    }
                }
            }
            exit() ;
        }

        public static function optimizeupdatedpreviousproduct() {
            if ( isset( $_POST[ 'ids' ] ) ) {
                $products = $_POST[ 'ids' ] ;
                foreach ( $products as $product ) {
                    $get_product = sumo_pyp_get_product( $product ) ;
                    if ( $get_product->is_type( 'simple' ) || ($get_product->is_type( 'subscription' )) ) {
                        if ( $_POST[ 'enableyourprice' ] == 'yes' ) {
                            update_post_meta( $product , '_checkboxvalue' , 'yes' ) ;
                        } else {
                            update_post_meta( $product , '_checkboxvalue' , 'no' ) ;
                        }
                        update_post_meta( $product , '_getminimumprice' , $_POST[ 'minimumprice' ] ) ;
                        update_post_meta( $product , '_hideminimum' , $_POST[ 'hideminimumprice' ] ) ;
                        update_post_meta( $product , '_getrecommendedprice' , $_POST[ 'recommendedprice' ] ) ;
                        update_post_meta( $product , '_getmaximumprice' , $_POST[ 'maximumprice' ] ) ;
                        update_post_meta( $product , '_hidemaximum' , $_POST[ 'hidemaximumprice' ] ) ;
                    } else {
                        if ( $get_product->is_type( 'variable' ) || ($get_product->is_type( 'variable-subscription' )) ) {
                            $variation = $get_product->get_available_variations() ;
                            $hideminprice = $_POST[ 'hideminimumprice' ] ;
                            $hideminprice = $hideminprice == 'yes' ? '2' : '1' ;

                            $hidemaxprice = $_POST[ 'hidemaximumprice' ] ;
                            $hidemaxprice = $hidemaxprice == 'yes' ? '2' : '1' ;

                            if ( is_array( $variation ) ) {
                                foreach ( $variation as $getvariation ) {
                                    if ( $_POST[ 'enableyourprice' ] == 'yes' ) {
                                        update_post_meta( $getvariation[ 'variation_id' ] , '_selectpayyourprice' , 'two' ) ;
                                    } else {
                                        update_post_meta( $getvariation[ 'variation_id' ] , '_selectpayyourprice' , 'three' ) ;
                                    }
                                    update_post_meta( $getvariation[ 'variation_id' ] , '_minimumprice' , $_POST[ 'minimumprice' ] ) ;
                                    update_post_meta( $getvariation[ 'variation_id' ] , '_hideminimumprice' , $hideminprice ) ;
                                    update_post_meta( $getvariation[ 'variation_id' ] , '_recommendedprice' , $_POST[ 'recommendedprice' ] ) ;
                                    update_post_meta( $getvariation[ 'variation_id' ] , '_maximumprice' , $_POST[ 'maximumprice' ] ) ;
                                    update_post_meta( $getvariation[ 'variation_id' ] , '_hidemaximumprice' , $hidemaxprice ) ;
                                }
                            }
                        }
                    }
                }
            }
            exit() ;
        }

    }

    FP_PYP_Load_Ajax::init() ;

endif;