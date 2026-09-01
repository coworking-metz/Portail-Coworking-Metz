<?php

/**
 * Conseil de formule sur la boutique.
 *
 * Regarde le rythme de présence du membre connecté (API tickets, /members/<id>/activity)
 * et affiche en haut de « La Boutique » un message qui recommande la formule la plus
 * avantageuse, chiffres à l'appui : carnet de 10 journées pour une venue à temps partiel,
 * abonnement 30 jours quand le carnet reviendrait plus cher.
 *
 * Deux choses ne sont pas écrites en dur :
 *  - le seuil de bascule, déduit des prix réels des deux produits, donc il suit les tarifs ;
 *  - la période analysée, qui par défaut écarte les mois de vacances scolaires (zone B,
 *    académie de Nancy-Metz) pour ne pas juger un rythme sur un mois de congés.
 *
 * Utilisable aussi manuellement via le shortcode [conseil-formule].
 */

// Nombre de mois retenus pour l'analyse par défaut.
const CONSEIL_FORMULE_MOIS_ANALYSES = 2;

// Profondeur du sélecteur de période, en mois.
const CONSEIL_FORMULE_MOIS_MAX = 24;

// Un mois contenant au moins ce nombre de jours de vacances scolaires est écarté.
const CONSEIL_FORMULE_JOURS_VACANCES = 7;

// Nombre de journées contenues dans un carnet.
const CONSEIL_FORMULE_JOURNEES_CARNET = 10;

// Durée de vie du cache du rythme de présence (l'appel /activity est volumineux).
const CONSEIL_FORMULE_TTL = 6 * HOUR_IN_SECONDS;


/**
 * ---------------------------------------------------------------------------
 * Produits comparés
 * ---------------------------------------------------------------------------
 */

/**
 * Les deux produits à comparer, selon que le membre a droit ou non aux tarifs réduits.
 *
 * @return array{carnet:int, abonnement:int}
 */
function conseil_formule_produits()
{
    // Éligibilité au tarif réduit = champ ACF tarifs_reduits_ok du compte.
    $tarif_reduit = (bool) current_user_can_tarif_reduit();

    $standard = ['carnet' => 35572, 'abonnement' => 35567]; // Catégorie « Nouveaux tarifs 2024 »
    $reduit   = ['carnet' => 35576, 'abonnement' => 35578]; // Catégorie « Tarifs réduits »

    $ids = apply_filters(
        'conseil_formule_produits',
        $tarif_reduit ? $reduit : $standard,
        $tarif_reduit
    );

    // Filet de sécurité : la catégorie « Tarifs réduits » est réservée aux membres
    // éligibles, et de toute façon refusée au panier par tarifs-reduits.php. On ne la
    // propose donc jamais aux autres, quels que soient les identifiants ci-dessus.
    if (!$tarif_reduit) {
        foreach ($ids as $role => $product_id) {
            if (has_term('tarifs-reduits', 'product_cat', $product_id)) {
                $ids[$role] = $standard[$role];
            }
        }
    }

    return $ids;
}


/**
 * ---------------------------------------------------------------------------
 * Période analysée
 * ---------------------------------------------------------------------------
 */

/**
 * Périodes de vacances scolaires de la zone B (académie de Nancy-Metz, dont Metz),
 * depuis 2017, lues dans le calendrier officiel au format ICS.
 *
 * fetch_holidays() de dates.inc.php lit la même source mais ne garde que les vacances
 * à venir ; ici on a besoin du passé.
 *
 * @return array<array{debut:int, fin:int}>|null Bornes en timestamps, fin exclusive.
 *                                               null si le calendrier est injoignable.
 */
function conseil_formule_vacances()
{
    $cle = 'conseil_formule_vacances_zone_b';

    $cache = get_transient($cle);
    if (is_array($cache)) {
        return $cache;
    }

    $reponse = wp_remote_get(
        'https://fr.ftp.opendatasoft.com/openscol/fr-en-calendrier-scolaire/Zone-B.ics',
        ['timeout' => 10]
    );

    if (is_wp_error($reponse) || 200 !== wp_remote_retrieve_response_code($reponse)) {
        error_log('conseil_formule_vacances : calendrier scolaire zone B injoignable');
        return null;
    }

    preg_match_all(
        '/DTSTART;VALUE=DATE:(\d{8})\s+DTEND;VALUE=DATE:(\d{8})/',
        wp_remote_retrieve_body($reponse),
        $trouves,
        PREG_SET_ORDER
    );

    $periodes = [];
    foreach ($trouves as $t) {
        $debut = strtotime($t[1]);
        $fin   = strtotime($t[2]);
        if ($debut && $fin) {
            $periodes[] = ['debut' => $debut, 'fin' => $fin];
        }
    }

    if (!$periodes) {
        return null;
    }

    // Certaines périodes se chevauchent (l'été et la prérentrée des enseignants) : on
    // les fusionne pour ne pas compter deux fois les mêmes jours.
    usort($periodes, fn($a, $b) => $a['debut'] <=> $b['debut']);

    $fusionnees = [];
    foreach ($periodes as $periode) {
        $derniere = count($fusionnees) - 1;
        if ($derniere >= 0 && $periode['debut'] <= $fusionnees[$derniere]['fin']) {
            $fusionnees[$derniere]['fin'] = max($fusionnees[$derniere]['fin'], $periode['fin']);
            continue;
        }
        $fusionnees[] = $periode;
    }

    set_transient($cle, $fusionnees, 30 * DAY_IN_SECONDS);

    return $fusionnees;
}


/**
 * Nombre de jours de vacances scolaires tombant dans un mois donné.
 *
 * @param int   $debut_mois Timestamp du 1er du mois.
 * @param array $vacances   Périodes retournées par conseil_formule_vacances().
 */
function conseil_formule_jours_vacances($debut_mois, array $vacances)
{
    $fin_mois = strtotime('+1 month', $debut_mois);
    $jours    = 0;

    foreach ($vacances as $periode) {
        $debut = max($periode['debut'], $debut_mois);
        $fin   = min($periode['fin'], $fin_mois);
        if ($fin > $debut) {
            $jours += (int) round(($fin - $debut) / DAY_IN_SECONDS);
        }
    }

    return $jours;
}


/**
 * Les mois proposés dans le sélecteur : les 24 derniers mois civils complets.
 *
 * @return array<string,int> « 2026-06 » => timestamp du 1er juin 2026, du plus récent au plus ancien.
 */
function conseil_formule_mois_selectionnables()
{
    $mois    = [];
    $dernier = strtotime('first day of last month 00:00:00');

    for ($i = 0; $i < CONSEIL_FORMULE_MOIS_MAX; $i++) {
        $ts = strtotime("-$i month", $dernier);
        $mois[date('Y-m', $ts)] = $ts;
    }

    return $mois;
}


/**
 * La plage choisie dans le sélecteur, sous forme de timestamps de 1ers de mois.
 *
 * @return array<int>|null null si aucune plage valide n'est demandée.
 */
function conseil_formule_periode_choisie()
{
    $debut = isset($_GET['cf_debut']) ? sanitize_text_field(wp_unslash($_GET['cf_debut'])) : '';
    $fin   = isset($_GET['cf_fin']) ? sanitize_text_field(wp_unslash($_GET['cf_fin'])) : '';

    $choix = conseil_formule_mois_selectionnables();
    if (!isset($choix[$debut], $choix[$fin])) {
        return null;
    }

    if ($debut > $fin) {
        [$debut, $fin] = [$fin, $debut];
    }

    $mois    = [];
    $courant = $choix[$debut];
    while ($courant <= $choix[$fin] && count($mois) < CONSEIL_FORMULE_MOIS_MAX) {
        $mois[]  = $courant;
        $courant = strtotime('+1 month', $courant);
    }

    return $mois ?: null;
}


/**
 * « en mai et juin 2026 », « de janvier à juin 2026 » : la période, telle qu'elle
 * s'insère dans une phrase.
 */
function conseil_formule_libelle_periode(array $mois, $plage)
{
    if (!$mois) {
        return '';
    }

    $premier = reset($mois);
    $dernier = end($mois);

    if ($premier === $dernier) {
        return 'en ' . date_i18n('F Y', $premier);
    }

    $meme_annee = date('Y', $premier) === date('Y', $dernier);
    $debut      = $meme_annee ? date_i18n('F', $premier) : date_i18n('F Y', $premier);

    if ($plage || count($mois) > 2) {
        return 'de ' . $debut . ' à ' . date_i18n('F Y', $dernier);
    }

    return 'en ' . $debut . ' et ' . date_i18n('F Y', $dernier);
}


/**
 * Les mois sur lesquels analyser la fréquentation.
 *
 * Par défaut : les deux derniers mois civils complets qui ne contenaient pas au moins
 * une semaine de vacances scolaires, pour ne pas juger un rythme sur un mois de congés.
 * Si l'utilisateur a choisi une plage dans le sélecteur, on prend cette plage telle
 * quelle, vacances comprises.
 *
 * @return array{mois:array<int>, libelle:string, personnalisee:bool}
 */
function conseil_formule_periode()
{
    $choisie = conseil_formule_periode_choisie();

    if ($choisie) {
        return [
            'mois'          => $choisie,
            'libelle'       => conseil_formule_libelle_periode($choisie, true),
            'personnalisee' => true,
        ];
    }

    $vacances = conseil_formule_vacances();
    $dernier  = strtotime('first day of last month 00:00:00');
    $mois     = [];

    // On remonte au maximum deux ans en arrière pour trouver assez de mois « propres ».
    for ($i = 0; $i < CONSEIL_FORMULE_MOIS_MAX && count($mois) < CONSEIL_FORMULE_MOIS_ANALYSES; $i++) {
        $candidat = strtotime("-$i month", $dernier);

        // Sans calendrier scolaire, on se rabat sur les derniers mois complets.
        if (null !== $vacances
            && conseil_formule_jours_vacances($candidat, $vacances) >= CONSEIL_FORMULE_JOURS_VACANCES) {
            continue;
        }

        $mois[] = $candidat;
    }

    sort($mois);

    return [
        'mois'          => $mois,
        'libelle'       => conseil_formule_libelle_periode($mois, false),
        'personnalisee' => false,
    ];
}


/**
 * ---------------------------------------------------------------------------
 * Rythme de présence
 * ---------------------------------------------------------------------------
 */

/**
 * Journées de présence sur les mois analysés, et moyenne mensuelle.
 *
 * @param int   $user_id
 * @param array $mois    Timestamps des 1ers de mois.
 * @return array{journees:float, journees_par_mois:float}|null null si l'API ne répond pas
 *                                                             ou si le membre est inconnu.
 */
function conseil_formule_rythme($user_id, array $mois)
{
    if (!$mois) {
        return null;
    }

    $cle = 'conseil_formule_' . (int) $user_id . '_' . substr(md5(implode(',', $mois)), 0, 10);

    $cache = get_transient($cle);
    if (is_array($cache)) {
        return $cache;
    }

    $activite = tickets('/members/' . (int) $user_id . '/activity');
    if (!is_array($activite)) {
        // Membre inconnu (404) ou API indisponible : on ne conseille rien, et on ne
        // met pas l'échec en cache.
        return null;
    }

    $bornes = [];
    foreach ($mois as $debut) {
        $bornes[] = [$debut, strtotime('+1 month', $debut)];
    }

    $journees = 0.0;
    foreach ($activite as $presence) {
        $date = strtotime($presence['date'] ?? '');
        if (!$date) {
            continue;
        }
        foreach ($bornes as $borne) {
            if ($date >= $borne[0] && $date < $borne[1]) {
                $journees += (float) ($presence['value'] ?? 0);
                break;
            }
        }
    }

    $rythme = [
        'journees'          => $journees,
        'journees_par_mois' => $journees / count($mois),
    ];

    set_transient($cle, $rythme, CONSEIL_FORMULE_TTL);

    return $rythme;
}


/**
 * ---------------------------------------------------------------------------
 * Mise en forme
 * ---------------------------------------------------------------------------
 */

/**
 * Formate un nombre de journées : 12.0 -> « 12 », 2.5 -> « 2,5 ».
 */
function conseil_formule_nombre($valeur)
{
    $arrondi = round($valeur * 2) / 2;

    return number_format_i18n($arrondi, fmod($arrondi, 1) == 0 ? 0 : 1);
}


/**
 * « 1 journée », « 2,5 journées » : accorde le nom avec la valeur arrondie.
 */
function conseil_formule_quantite($valeur, $singulier, $pluriel)
{
    $arrondi = round($valeur * 2) / 2;

    return conseil_formule_nombre($arrondi) . ' ' . ($arrondi >= 2 ? $pluriel : $singulier);
}


/**
 * Un montant en texte brut, utilisable dans un titre passé à esc_html() : wc_price()
 * renvoie du HTML avec des entités, qui s'afficheraient telles quelles une fois échappées.
 */
function conseil_formule_montant($montant)
{
    return html_entity_decode(wp_strip_all_tags(wc_price($montant)), ENT_QUOTES, 'UTF-8');
}


/**
 * « (soit environ 3 jours par semaine) », ou une chaîne vide quand le rythme est trop
 * faible pour que la moyenne hebdomadaire veuille dire quelque chose.
 */
function conseil_formule_par_semaine($journees_par_mois)
{
    $par_semaine = $journees_par_mois * 7 / 30;

    if ($par_semaine < 1) {
        return '';
    }

    return ' (soit environ ' . conseil_formule_quantite($par_semaine, 'jour', 'jours') . ' par semaine)';
}


/**
 * Aperçu réservé aux administrateurs, pour vérifier le rendu depuis un compte qui ne
 * verrait normalement rien :
 *
 *   ?conseil-formule=1    affiche le bloc même si le membre est déjà abonné
 *   ?conseil-formule=13   simule en plus un rythme de 13 journées par mois
 *
 * @return float|null null hors mode aperçu.
 */
function conseil_formule_apercu()
{
    if (!isset($_GET['conseil-formule']) || !current_user_can('administrator')) {
        return null;
    }

    return (float) $_GET['conseil-formule'];
}


/**
 * Le sélecteur de plage et le rappel de la période, affichés en pied du bloc.
 */
function conseil_formule_selecteur(array $periode)
{
    $choix   = conseil_formule_mois_selectionnables();
    $mois    = $periode['mois'];
    $courant = [date('Y-m', reset($mois)), date('Y-m', end($mois))];
    $action  = remove_url_parameter(remove_url_parameter(get_permalink(), 'cf_debut'), 'cf_fin');

    $options = function ($selection) use ($choix) {
        $html = '';
        foreach ($choix as $valeur => $ts) {
            $html .= '<option value="' . esc_attr($valeur) . '"' . selected($valeur, $selection, false) . '>'
                . esc_html(date_i18n('F Y', $ts)) . '</option>';
        }
        return $html;
    };

    ob_start();
    ?>
    <div class="conseil-formule__pied">
        <?php // Replié par défaut ; déjà ouvert si une plage personnalisée est en cours.
              // Une fois ouvert, le lien s'efface (CSS) pour ne laisser que le formulaire :
              // la période analysée est de toute façon nommée dans la phrase du conseil. ?>
        <details class="conseil-formule__reglage"<?php echo $periode['personnalisee'] ? ' open' : ''; ?>>
            <summary class="conseil-formule__bascule">Voir le calcul sur une autre période</summary>

            <form class="conseil-formule__selecteur" method="get" action="<?php echo esc_url($action); ?>">
                <?php if (null !== conseil_formule_apercu()) : ?>
                    <input type="hidden" name="conseil-formule"
                           value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_GET['conseil-formule']))); ?>">
                <?php endif; ?>
                <label for="cf_debut">De</label>
                <select id="cf_debut" name="cf_debut"><?php echo $options($courant[0]); ?></select>
                <label for="cf_fin">à</label>
                <select id="cf_fin" name="cf_fin"><?php echo $options($courant[1]); ?></select>
                <button type="submit">Recalculer</button>
                <?php if ($periode['personnalisee']) : ?>
                    <a class="conseil-formule__retour" href="<?php echo esc_url($action); ?>">Période par défaut</a>
                <?php endif; ?>
            </form>
        </details>
    </div>
    <?php

    return ob_get_clean();
}


/**
 * Le message de conseil, ou une chaîne vide s'il n'y a rien de pertinent à dire.
 *
 * @return string
 */
function conseil_formule_html()
{
    if (!is_user_logged_in() || !function_exists('wc_get_product')) {
        return '';
    }

    $user_id = get_current_user_id();
    $apercu  = conseil_formule_apercu();

    // Un membre déjà abonné n'a pas besoin qu'on lui conseille une formule.
    $membre = tickets('/members/' . $user_id);
    if (null === $apercu && is_array($membre) && !empty($membre['hasActiveSubscription'])) {
        return '';
    }

    $periode = conseil_formule_periode();
    $rythme  = conseil_formule_rythme($user_id, $periode['mois']);

    // ?conseil-formule=<nombre> : rythme simulé, pour visualiser les autres messages.
    if (null !== $apercu && $apercu > 1) {
        $rythme = ['journees' => $apercu * count($periode['mois']), 'journees_par_mois' => $apercu];
    }

    if (null === $rythme) {
        return '';
    }

    $produits   = conseil_formule_produits();
    $carnet     = wc_get_product($produits['carnet']);
    $abonnement = wc_get_product($produits['abonnement']);
    if (!$carnet || !$abonnement) {
        return '';
    }

    $prix_carnet     = (float) $carnet->get_price();
    $prix_abonnement = (float) $abonnement->get_price();
    if ($prix_carnet <= 0 || $prix_abonnement <= 0) {
        return '';
    }

    $prix_journee = $prix_carnet / CONSEIL_FORMULE_JOURNEES_CARNET;

    // Au-delà de ce nombre de journées par mois, le carnet revient plus cher que l'abonnement.
    $seuil = $prix_abonnement / $prix_journee;

    $par_mois = $rythme['journees_par_mois'];

    // Ce que coûteraient, en journées de carnet, les 30 prochains jours au même rythme,
    // et l'écart avec le prix d'un abonnement sur la même période.
    $cout_carnet = $par_mois * $prix_journee;
    $gain_abo    = $cout_carnet - $prix_abonnement;

    $ouverture = ucfirst($periode['libelle']) . ', vous avez cumulé en moyenne';

    $lien = function ($produit, $libelle) {
        return '<a class="conseil-formule__cta" href="' . esc_url($produit->get_permalink()) . '">'
            . esc_html($libelle) . '</a>';
    };

    if ($rythme['journees'] <= 0) {

        // Aucune présence sur la période : on oriente vers la formule la plus souple.
        $titre = 'Quelle formule choisir ?';
        $texte = sprintf(
            'Sans engagement, le <strong>%1$s</strong> à %2$s (%3$s la journée) est la formule la plus souple pour commencer. L&rsquo;<strong>%4$s</strong> à %5$s devient plus intéressant à partir de %6$s de présence par mois.',
            esc_html($carnet->get_name()),
            wc_price($prix_carnet),
            wc_price($prix_journee),
            esc_html($abonnement->get_name()),
            wc_price($prix_abonnement),
            conseil_formule_quantite($seuil, 'journée', 'journées')
        );
        $cta = $lien($carnet, 'Voir le carnet');

    } elseif ($gain_abo > 0.01) {

        // L'abonnement coûte moins cher que les mêmes journées payées en carnet.
        $titre = 'Vous économiseriez ' . conseil_formule_montant($gain_abo) . ' par mois avec l\'abonnement';
        $texte = sprintf(
            '%1$s <strong>%2$s de présence par mois</strong>%3$s. Si vous gardez ce rythme, ces journées vous coûteraient environ <strong>%4$s</strong> en carnet sur les 30 prochains jours, contre <strong>%5$s</strong> avec l&rsquo;<strong>%6$s</strong> : environ <strong>%7$s d&rsquo;économie</strong>, et vous venez autant que vous voulez.',
            esc_html($ouverture),
            conseil_formule_quantite($par_mois, 'journée', 'journées'),
            conseil_formule_par_semaine($par_mois),
            wc_price($cout_carnet),
            wc_price($prix_abonnement),
            esc_html($abonnement->get_name()),
            wc_price($gain_abo)
        );
        $cta = $lien($abonnement, 'Voir l\'abonnement');

    } elseif ($gain_abo >= -0.01) {

        // Pile au point d'équilibre : à prix égal on pousse l'abonnement, qui donne plus
        // pour le même budget — sans promettre une économie qui n'existe pas.
        $titre = 'À prix égal, l\'abonnement vous en donne plus';
        $texte = sprintf(
            '%1$s <strong>%2$s de présence par mois</strong>%3$s. À ce rythme, carnet et abonnement vous coûtent exactement la même chose sur 30 jours (environ <strong>%4$s</strong>) — mais l&rsquo;<strong>%5$s</strong> vous laisse venir autant de jours que vous voulez, sans compter vos tickets.',
            esc_html($ouverture),
            conseil_formule_quantite($par_mois, 'journée', 'journées'),
            conseil_formule_par_semaine($par_mois),
            wc_price($prix_abonnement),
            esc_html($abonnement->get_name())
        );
        $cta = $lien($abonnement, 'Voir l\'abonnement');

    } else {

        // Le carnet coûte moins cher qu'un abonnement pour ce rythme de présence.
        $titre = 'Vous économiseriez ' . conseil_formule_montant(-$gain_abo) . ' par mois avec le carnet';
        $texte = sprintf(
            '%1$s <strong>%2$s de présence par mois</strong>%3$s. Si vous gardez ce rythme, ces journées vous coûteraient environ <strong>%4$s</strong> avec le <strong>%5$s</strong> (%6$s la journée) sur les 30 prochains jours, contre <strong>%7$s</strong> pour un abonnement : environ <strong>%8$s d&rsquo;économie</strong>, puisque vous ne payez que vos jours de présence. L&rsquo;abonnement redevient intéressant à partir de %9$s par mois.',
            esc_html($ouverture),
            conseil_formule_quantite($par_mois, 'journée', 'journées'),
            conseil_formule_par_semaine($par_mois),
            wc_price($cout_carnet),
            esc_html($carnet->get_name()),
            wc_price($prix_journee),
            wc_price($prix_abonnement),
            wc_price(-$gain_abo),
            conseil_formule_quantite($seuil, 'journée', 'journées')
        );
        $cta = $lien($carnet, 'Voir le carnet');
    }

    // .ld-container.container : le conteneur du thème, pour que le bloc s'aligne sur le
    // reste du contenu. Sans lui, le bloc est inséré nu dans <main class="content"> et
    // s'étale sur toute la largeur de l'écran.
    return '<div class="ld-container container conseil-formule-conteneur">'
        . '<div class="conseil-formule">'
        . '<div class="conseil-formule__principal">'
        . '<div class="conseil-formule__contenu">'
        . '<p class="conseil-formule__titre">' . esc_html($titre) . '</p>'
        . '<p class="conseil-formule__texte">' . wp_kses_post($texte) . '</p>'
        . '</div>'
        . '<p class="conseil-formule__actions">' . $cta . '</p>'
        . '</div>'
        . conseil_formule_selecteur($periode)
        . '</div>'
        . '</div>';
}


add_shortcode('conseil-formule', 'conseil_formule_html');


/**
 * Insertion automatique en haut du contenu de la page « La Boutique », donc juste
 * sous le titre.
 */
add_filter('the_content', function ($content) {

    if (is_admin() || !in_the_loop() || !is_main_query() || !is_page('la-boutique')) {
        return $content;
    }

    return conseil_formule_html() . $content;
}, 9);
