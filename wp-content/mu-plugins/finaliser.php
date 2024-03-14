<?php

/**
 * Procédure d'auto validation du compte coworker. 
 * Apres une visite, chaque personne reçoit un mail automatique invitant à activer 
 * son compte ( = se créer un mot de passe et passer au statut coworker pour pouvoir passer commandes).
 * Le lien de validation du compte arrive ici
 * 
 */
if (isset($_GET['validation-compte']) && isset($_GET['uid'])) {

    add_action('init', function() {

        $hash = $_GET['validation-compte'];
        $uid = $_GET['uid'];
        
        // vérification du hash afin de ne pas valider un autre compte que celui qui est prévu par ce lien
        if(sha1($uid.AUTH_SALT) != $hash) {
            wp_redirect('/');
            exit;
        }

        // finaliser l'utilisateur
        $status = finaliser_user($uid);
        
        // Rediriger vers une page qui affichera une notification relative au status retourné
        wp_redirect('/?status-validation-compte='.$status);
        exit;
    });

}

/**
 * Afficher une notification relative au status retourné lors de la validation d'un compte
 * 
 */
if (isset($_GET['status-validation-compte'])) {
    add_action('wp_footer', function() {
        $status = $_GET['status-validation-compte']??false;
        $details = finaliser_status_details($status);
        echo generateNotification([
            'type' => $details['type'],
            'titre' => $details['title'],
            'texte' => $details['subtitle']
        ]);
    });
}

/**
 * Définition du créneau d'éxécution de l'envoi des mails recap de visites : tous les jours à 19h00
 */
add_action('init', function() {
    if (!wp_next_scheduled('cron_envoyer_mails_recap_de_vistes')) {
        wp_schedule_event(strtotime('19:00:00'), 'daily', 'cron_envoyer_mails_recap_de_vistes');
    }
});

/**
 * Hook d'Exécution de l'envoi des mails recap de visites
 */
add_action('cron_envoyer_mails_recap_de_vistes', function() {
    $total = envoyer_mails_recap_de_vistes();
    echo $total.' cron_envoyer_mails_recap_de_vistes';
});

/**
 * Envoie un email récapitulatif des visites aux utilisateurs qui ont effectué une visite aujourd'hui, et qui ne sont pas déjà customers
 *
 * @return int Le nombre d'emails envoyés.
 */
function envoyer_mails_recap_de_vistes() {
    $cpt=0;
    $visiteurs = fetch_users_with_visite_today();

    foreach ($visiteurs as $visiteur) {

        // on ignore les utilisateurs qui sont déjà au statut customer : ils ont déjà été validés manuellement
        if (in_array('customer', $visiteur->roles))
            continue;

        if(envoyerMailRecapVisite($visiteur->ID)) {
            $cpt++;
        }
    }

    return $cpt;
}
/**
 * Récupère l'ID de l'utilisateur à partir de la requête GET.
 *
 * Si l'ID de l'utilisateur est défini et que le paramètre 'finaliser' est présent dans la requête GET,
 * l'action 'init' est ajoutée pour finaliser le compte de l'utilisateur.
 * Si le paramètre 'status_finaliser' est présent dans la requête GET, une notification est affichée dans
 * l'admin en fonction de la valeur de 'status_finaliser'.
 *
 * @global array $_GET Les données de la requête GET.
 * 
 * @var int|bool $user_id L'ID de l'utilisateur ou false si non défini.
 * @var int|bool $status Le statut de la finalisation (1: succès, -1: déjà finalisé, -2: erreur d'envoi du mail) ou false si non défini.
 */

$user_id = $_GET['user_id'] ?? false;
if ($user_id) {
    if (isset($_GET['finaliser'])) {
        add_action('init', function () use ($user_id) {

            $status = finaliser_compte($user_id);
            wp_redirect(admin_url('user-edit.php?status_finaliser=' . $status . '&user_id=' . $user_id));
        });
    }

    if (isset($_GET['status_finaliser'])) {
        $status = $_GET['status_finaliser'] ?? false;
        add_action('admin_notices', function () use ($status) {
            $details = finaliser_status_details($status);
            if ($details) {
?>
                <div class="notice notice-<?=$details['type'];?> is-dismissible">
                    <p style="font-size:150%"><strong><?=$details['title'];?></strong></p>
                    <p style="font-size:150%"><?=$details['description'];?></p>
                </div>
            <?php
            
            }
        });
    }
}
