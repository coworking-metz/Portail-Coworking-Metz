<?php



/**
 * Filtrer la page des visites pour ne garder que les users ayant une visite future (et on met aussi toutes les visites de la semaine passée)
 */
if (isset($_GET['nomadesOnly'])) {
    add_action('pre_get_users', function ($query) {
        if (is_admin()) {
            $query->set('meta_key', 'datePresence');
            $query->set('meta_value', date('Y-m-d H:i:s', strtotime('last monday')));
            $query->set('meta_compare', '>');
        }
    });
}