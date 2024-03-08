<?php

/**
 * Recuperer tous les emails qui se sont désabonnés dans mailchimp, et importer cette info dans brevo
 * (tache planifiée tous les jours une fois par jour)
 */
function sync_users_to_brevo() {
    print_r(brevo_sync_to_wordpress_list());
}
function setup_daily_sync_users_to_brevo() {
    if (!wp_next_scheduled('sync_users_to_brevo')) {
        wp_schedule_event(time(), 'daily', 'sync_users_to_brevo');
    }
}
add_action('wp', 'setup_daily_sync_users_to_brevo');
add_action('sync_users_to_brevo', 'sync_users_to_brevo');


/**
 * Recuperer tous les emails qui se sont désabonnés dans mailchimp, et importer cette info dans brevo
 * (tache planifiée tous les jours une fois par jour)
 */
function sync_unsubscribed_from_mailchimp_to_brevo() {
    $unsubs = mailchimp_unsubscribed();
    $ret = brevo_unsubscribe($unsubs);
    print_r($ret);
}
function setup_daily_unsubscribes_mailchimp_to_brevo() {
    if (!wp_next_scheduled('sync_unsubscribed_from_mailchimp_to_brevo')) {
        wp_schedule_event(time(), 'daily', 'sync_unsubscribed_from_mailchimp_to_brevo');
    }
}
add_action('wp', 'setup_daily_unsubscribes_mailchimp_to_brevo');
add_action('sync_unsubscribed_from_mailchimp_to_brevo', 'sync_unsubscribed_from_mailchimp_to_brevo');


/**
 * Recuperer tous les emails qui se sont désabonnés dans brevo, et importer cette info dans mailchimp
 * (tache planifiée tous les jours une fois par jour)
 */
function sync_unsubscribed_from_brevo_to_mailchimp() {
    /*
    tâche asynchrone, on lance ici la démarche, puis brevo va apeller une url de notif sur wp qui se chargera de faire le travail
    Possibilité de suivre les appels à l'url de notify via ce request bin 
    https://eojss26rcl22wc9.m.pipedream.net (se connecter avec le gmail du cowo)
    */
    brevo_start_unsubscribed(); 
}
function setup_daily_unsubscribes_brevo_to_mailchimp() {
    if (!wp_next_scheduled('sync_unsubscribed_from_brevo_to_mailchimp')) {
        wp_schedule_event(time(), 'daily', 'sync_unsubscribed_from_brevo_to_mailchimp');
    }
}
add_action('wp', 'setup_daily_unsubscribes_brevo_to_mailchimp');
add_action('sync_unsubscribed_from_brevo_to_mailchimp', 'sync_unsubscribed_from_brevo_to_mailchimp');


