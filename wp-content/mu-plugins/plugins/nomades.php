<?php



/**
 * Filtrer la page des visites pour ne garder que les users ayant une visite future (et on met aussi toutes les visites de la semaine passée)
 */
if (isset($_GET['nomadesOnly'])) {
/*    add_action('pre_get_users', function ($query) {
        if (is_admin()) {
            $query->set('meta_key', 'nomade');
            $query->set('meta_value', '');
            $query->set('meta_compare', '!=');
        }
    });*/


	add_action('pre_get_users', function ($query) {
    if (is_admin()) {
        global $wpdb;
        $user_ids = $wpdb->get_col("
            SELECT DISTINCT postmeta.meta_value 
            FROM {$wpdb->posts} AS posts
            INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
            WHERE posts.post_type = 'shop_order'
            AND posts.post_status IN ('wc-completed', 'wc-processing', 'wc-on-hold')
            AND postmeta.meta_key = '_customer_user'
            AND postmeta.meta_value != '0'
        ");

        if (!empty($user_ids)) {
            $query->query_vars['include'] = $user_ids;
        } else {
            $query->query_vars['include'] = [0]; // Empêche d'afficher des utilisateurs
        }

	        $query->set('meta_key', 'nomade');
            $query->set('meta_value', '1');
            $query->set('meta_compare', '=');

}
});

}

add_filter( 'manage_users_columns', function( $columns )
{
    return array_slice( $columns, 0, 2, true ) 
        + [ 'custom_name' => __( 'Name' ) ] 
        + array_slice( $columns, 3, null, true );
} );


add_filter( 'manage_users_custom_column', function( $output, $column_name, $user_id )
{
    if( 'custom_name' === $column_name )
    {
		$u = new WP_User( $user_id ); 
        if( $u instanceof \WP_User )
        {
            // Default output
            $output .= "$u->first_name $u->last_name";
			if(get_field('nomade','user_'.$user_id)) {
            // Extra output
				$output .= "<br><b>🕰️ nomade</b>";
			}


            // Housecleaning
            unset( $u ); 
        }
    }       
    return $output;
}, 10, 3 );   