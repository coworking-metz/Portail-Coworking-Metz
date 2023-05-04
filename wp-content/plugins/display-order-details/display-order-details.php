<?php 
/*
Plugin Name: Display Order Details for WooCommerce
Description: This plugin displays the list of items in an order in the WooCommerce->Orders page
Version: 1.0
Author: Pinal Shah
*/

if ( ! class_exists( 'display_order_details' ) ) {
    
    class display_order_details {
        
        public function __construct() {
            
            add_filter( 'manage_edit-shop_order_columns', array( &$this, 'od_column_header' ), 10, 1 );
            add_action( 'manage_shop_order_posts_custom_column', array( &$this, 'od_column_value' ), 10, 1 );
        }
        
        function od_column_header( $columns ) {
            
            // get all columns up to Order
            $new_columns = array();
            foreach ( $columns as $name => $value ) {
                if ( $name == 'billing_address' ) {
                    prev( $columns );
                    break;
                }
                $new_columns[ $name ] = $value;
            }
            // inject our columns
            $new_columns[ 'dot_items' ] = __( 'Items', 'display-items' );
            // add the remaining columns
            foreach ( $columns as $name => $value ) {
                $new_columns[ $name ] = $value;
            }
             
            
            return $new_columns;
        }
        
        function od_column_value( $column ) {
            
            if ( $column == 'dot_items' ) {
                global $post;
                
                $item_link = '';
                $order_id = $post->ID;
                $order = wc_get_order( $order_id );
                $items = $order->get_items();
                
                foreach( $items as $item_value ) {
                    
                    $order_data = $item_value->get_data();
                    
                    $product_id = $order_data[ 'product_id' ];
                    $product_name = $order_data[ 'name' ];
                    $quantity = $order_data[ 'quantity' ];
                    
                    $args[ 'post' ] = $product_id;
                    $args[ 'action' ] = 'edit';
                    
                    $item_link .= "<a href='" . esc_url_raw( add_query_arg( $args, get_admin_url( null, 'post.php' ) ) ) . "'>$product_name</a> x $quantity<br>"; 
                }
                
                echo $item_link;
            }

        }
    } // end of class
} 
$display_order_details = new display_order_details();
?>