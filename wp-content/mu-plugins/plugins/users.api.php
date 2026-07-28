<?php

/**
 * Options d'un compte pilotables depuis le manager.
 *
 * Ces valeurs vivent dans WordPress et y restent la reference : plusieurs sont
 * lues en direct par la boutique ou par les exports.
 *  - payer_en_virement : affiche ou non le moyen de paiement "Virement"
 *    @see plugins/woocommerce.php  (filtre woocommerce_available_payment_gateways)
 *  - tarifs_reduits_ok : autorise l'achat des produits "tarifs-reduits"
 *    @see plugins/tarifs-reduits.php
 *
 * Le manager passe par tickets-backend, qui appelle cet endpoint et ne repercute
 * la valeur dans MongoDB qu'une fois WordPress d'accord.
 *
 * On expose un endpoint dedie plutot que d'ecrire via /wp/v2/users?acf=... parce
 * qu'ACF maintient deux metas par champ (`nom` pour la valeur, `_nom` pour la cle
 * du champ). Seul update_field() tient les deux a jour.
 */

/**
 * Champs exposes, avec leur type. Le type pilote la validation et la coercition.
 *
 * bool   -> stocke en 1/0 (et non true/false : ACF ecrirait une chaine vide pour
 *           false, alors que les donnees existantes utilisent '0')
 * date   -> chaine Y-m-d, comme le return_format du champ ACF
 * text   -> chaine libre
 * select -> chaine, doit faire partie des choix declares dans le groupe ACF
 */
function cowo_user_options_schema()
{
    return [
        'payer_en_virement'    => 'bool',
        'tarifs_reduits_ok'    => 'bool',
        'candidat_au_ca'       => 'bool',
        'date_naissance'       => 'date',
        'polaroid_nom'         => 'text',
        'polaroid_description' => 'text',
        'statut_juridique'     => 'select',
        'type_activite'        => 'select',
    ];
}

/**
 * Lit les options d'un compte, typees.
 *
 * @param int $user_id
 * @return array
 */
function cowo_get_user_options($user_id)
{
    $cle = 'user_' . (int) $user_id;
    $options = [];

    foreach (cowo_user_options_schema() as $champ => $type) {
        $valeur = get_field($champ, $cle);

        switch ($type) {
            case 'bool':
                $options[$champ] = (bool) $valeur;
                break;
            case 'date':
            case 'text':
            case 'select':
            default:
                $options[$champ] = is_scalar($valeur) ? (string) $valeur : '';
                break;
        }
    }

    return $options;
}

/**
 * Choix disponibles pour les champs de type select, lus depuis la definition ACF.
 * Le manager en a besoin pour construire ses listes deroulantes : on evite ainsi
 * de dupliquer la liste des valeurs des deux cotes.
 *
 * @return array<string, array<string,string>>
 */
function cowo_get_user_options_choices()
{
    $choix = [];

    foreach (cowo_user_options_schema() as $champ => $type) {
        if ($type !== 'select') {
            continue;
        }

        // acf_get_field() lit la definition du champ independamment de tout
        // objet : pas besoin d'un utilisateur pour connaitre les choix.
        $objet = acf_get_field($champ);
        $choix[$champ] = ($objet && !empty($objet['choices'])) ? $objet['choices'] : [];
    }

    return $choix;
}

add_action('rest_api_init', function () {

    $permission = function () {
        return current_user_can('edit_users');
    };

    $verifier_utilisateur = function ($request) {
        $user_id = (int) $request['id'];

        if (!get_userdata($user_id)) {
            return new WP_Error('cowo_user_not_found', 'Utilisateur introuvable', ['status' => 404]);
        }

        return $user_id;
    };

    // Valeurs autorisees pour les champs a liste fermee. Independant d'un compte :
    // tickets-backend les met en cache et les sert a ses clients, qui n'ont ainsi
    // pas a savoir que la liste est definie dans ACF.
    register_rest_route('cowo/v1', '/user-options/choices', [
        'methods'             => 'GET',
        'permission_callback' => $permission,
        'callback'            => function () {
            return new WP_REST_Response(cowo_get_user_options_choices(), 200);
        },
    ]);

    register_rest_route('cowo/v1', '/users/(?P<id>\d+)/options', [
        [
            'methods'             => 'GET',
            'permission_callback' => $permission,
            'callback'            => function ($request) use ($verifier_utilisateur) {
                $user_id = $verifier_utilisateur($request);
                if (is_wp_error($user_id)) return $user_id;

                return new WP_REST_Response([
                    'values'  => cowo_get_user_options($user_id),
                    'choices' => cowo_get_user_options_choices(),
                ], 200);
            },
        ],
        [
            'methods'             => 'POST',
            'permission_callback' => $permission,
            'callback'            => function ($request) use ($verifier_utilisateur) {
                $user_id = $verifier_utilisateur($request);
                if (is_wp_error($user_id)) return $user_id;

                $cle    = 'user_' . $user_id;
                $schema = cowo_user_options_schema();
                $choix  = cowo_get_user_options_choices();

                foreach ($schema as $champ => $type) {
                    if (!$request->has_param($champ)) {
                        continue;
                    }

                    $brut = $request->get_param($champ);

                    switch ($type) {
                        case 'bool':
                            $valeur = (bool) $brut ? 1 : 0;
                            $courante = (bool) get_field($champ, $cle) ? 1 : 0;
                            break;

                        case 'date':
                            $valeur = trim((string) $brut);
                            if ($valeur !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $valeur)) {
                                return new WP_Error(
                                    'cowo_invalid_date',
                                    sprintf('Le champ %s attend une date au format AAAA-MM-JJ', $champ),
                                    ['status' => 400]
                                );
                            }
                            // ACF stocke les date_picker en Ymd et ne formate qu'a la
                            // lecture (return_format Y-m-d). Ecrire du Y-m-d en base
                            // casserait la coherence avec les comptes existants.
                            $valeur = $valeur === '' ? '' : str_replace('-', '', $valeur);
                            $courante = (string) get_field($champ, $cle, false);
                            break;

                        case 'select':
                            $valeur = trim((string) $brut);
                            if ($valeur !== '' && !array_key_exists($valeur, (array) $choix[$champ])) {
                                return new WP_Error(
                                    'cowo_invalid_choice',
                                    sprintf('Valeur non autorisee pour %s : %s', $champ, $valeur),
                                    ['status' => 400]
                                );
                            }
                            $courante = (string) get_field($champ, $cle);
                            break;

                        case 'text':
                        default:
                            $valeur = sanitize_text_field((string) $brut);
                            $courante = (string) get_field($champ, $cle);
                            break;
                    }

                    // On n'ecrit que sur changement reel, pour ne pas polluer la base.
                    if ((string) $courante !== (string) $valeur) {
                        update_field($champ, $valeur, $cle);
                    }
                }

                return new WP_REST_Response([
                    'values'  => cowo_get_user_options($user_id),
                    'choices' => $choix,
                ], 200);
            },
        ],
    ]);
});
