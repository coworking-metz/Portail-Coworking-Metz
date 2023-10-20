<?php


/**
 * Récupère l'ID du post à partir de données variées.
 *
 * @param mixed $data Peut être un ID numérique, un objet ou un tableau associatif contenant 'id', 'ID', 'pid', ou 'term_id'.
 * 
 * @return int|null L'ID du post ou null en cas d'échec.
 */
function get_post_id($data)
{
    $id = is_numeric($data) ? $data : si($data, ['id', 'ID', 'pid', 'term_id']);
    return $id;
}



/**
 * Get Post object by post_meta query
 *
 * @use         $post = get_post_by_meta( array( meta_key = 'page_name', 'meta_value = 'contact' ) )
 * @since       1.0.4
 * @return      Object      WP post object
 */
function get_post_by_meta($key, $value = null, $type = 'post', $status = false)
{

    $posts = get_posts_by_meta($key, $value, $type, 1, $status);

    if (!$posts || is_wp_error($posts)) return false;

    return $posts[0];
}



/**
 * Récupère les posts par une meta clé et valeur.
 *
 * @param string $key La clé meta à rechercher.
 * @param mixed $value La valeur de la clé meta à rechercher. Null pour n'importe quelle valeur.
 * @param string $type Le type de post à rechercher.
 * @param int $limit Le nombre maximal de posts à retourner.
 * @param bool|string $status Le statut des posts à retourner.
 * @param array $exclude Les ID des posts à exclure.
 * 
 * @return array|false Les posts correspondant aux critères ou false en cas d'échec.
 */
function get_posts_by_meta($key, $value = null, $type = 'post', $limit = -1, $status = false, $exclude = [])
{

    $args = array(
        'meta_query'        => array(
            array(
                'key'       => $key
            )
        ),
        'post_type'         => $type,
        'posts_per_page'    => $limit,
    );
    if ($exclude) {
        $args['post_status'] = $status;
    }
    if ($status) {
        $args['post_status'] = $status;
    }
    if (!is_null($value)) {
        $args['meta_query'][0]['value'] = $value;
    } else {
        $args['meta_query'][0]['value'] = [''];
        $args['meta_query'][0]['compare'] = 'NOT IN';
    }
    // run query ##
    $posts = get_posts($args);

    // check results ##
    if (!$posts || is_wp_error($posts)) return false;

    return $posts;
}