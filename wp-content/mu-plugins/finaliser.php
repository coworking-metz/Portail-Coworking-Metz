<?php


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
            $status = -3;
            $user = get_userdata($user_id);

            if ($user) {
                if (in_array('subscriber', $user->roles) || in_array('bookmify-customer', $user->roles)) {
                    $user->set_role('customer');
                    if (envoyer_email_creation_compte($user)) {
                        $status=1;
                        // wp_redirect(admin_url('user-edit.php?status_finaliser=1&user_id=' . $user_id));
                    } else $status = -2;
                } else $status = -1;
            }
            wp_redirect(admin_url('user-edit.php?status_finaliser=' . $status . '&user_id=' . $user_id));
        });
    }

    if (isset($_GET['status_finaliser'])) {
        $status = $_GET['status_finaliser'] ?? false;
        add_action('admin_notices', function () use ($status) {
            if ($status == 1) {
?>
                <div class="notice notice-success is-dismissible">
                    <p style="font-size:150%"><strong>Le compte adhérent a été finalisé</strong></p>
                    <p style="font-size:150%">Ce compte utilisateur a désormais le rôle "Coworker" et <a href="/wp-admin/admin.php?page=reglages-visites#tab-field_653279fcd5252">le mail de création de compte</a> lui a été envoyé. <a href="/wp-admin/admin.php?page=reglages-visites">Voir les options des visites</a></p>
                </div>
            <?php
            } else if ($status == -1) {
            ?>
                <div class="notice notice-warning is-dismissible">
                    <p style="font-size:150%"><strong>Compte adhérent déjà été finalisé</strong></p>
                    <p style="font-size:150%">Ce compte utilisateur n'avait pas un compte "En attente" et n'a donc pas été modifié. Le mail de création de compte ne lui a pas été ré-envoyé.</p>
                </div>
            <?php

            } else if ($status == -2) {
            ?>
                <div class="notice notice-error is-dismissible">
                    <p style="font-size:150%"><strong>Erreur d'envoi du mail</strong></p>
                    <p style="font-size:150%">Le <a href="/wp-admin/admin.php?page=reglages-visites#tab-field_653279fcd5252">mail de création de compte</a> n'a pas été envoyé à cause d'une erreur inconnue.</p>
                </div>
            <?php
            } else {
            ?>
                <div class="notice notice-error is-dismissible">
                    <p style="font-size:150%"><strong>Erreur de finalisation</strong></p>
                    <p style="font-size:150%">Une erreur inconnue s'est produite.</p>
                </div>
<?php

            }
        });
    }
}
