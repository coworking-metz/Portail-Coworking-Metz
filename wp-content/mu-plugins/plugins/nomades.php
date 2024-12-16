<?php



/**
 * Filtrer la page des visites pour ne garder que les users ayant une visite future (et on met aussi toutes les visites de la semaine passée)
 */
if (isset($_GET['nomadesOnly'])) {
    add_action('pre_get_users', function ($query) {
        if (is_admin()) {
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