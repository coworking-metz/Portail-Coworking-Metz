<?php
/**
 * api_user_balance_app();
 *
 * Récapitulatif du compte affiché en haut de la boutique, sous forme de tuiles :
 * tickets disponibles, abonnement en cours, présences cumulées, plus un rappel
 * « vérifiez votre solde avant d'acheter » avec un lien vers Mon compte.
 *
 * Appelé par le shortcode [phpcode slug=api_user_balance_app] de la page « La Boutique ».
 * Le style vit dans mu-plugins/css/front/boutique/boutique.css.
 */
function api_user_balance_app()
{
    $user_id = get_current_user_id();
    if (!$user_id) {
        return;
    }

    // tickets() met le résultat en cache pour la durée de la requête : sur la boutique
    // l'appel est déjà fait par le conseil de formule, celui-ci ne coûte donc rien.
    $membre = tickets('/members/' . $user_id);
    if (!is_array($membre)) {
        // Membre inconnu de la billetterie ou API indisponible : on n'affiche rien
        // plutôt qu'un bloc vide ou une erreur.
        return;
    }

    $balance   = (float) ($membre['balance'] ?? 0);
    $presences = (float) ($membre['presencesJours'] ?? 0);
    $abo_actif = !empty($membre['hasActiveSubscription']);
    $fin_abo   = $abo_actif && !empty($membre['lastAboEnd']) ? strtotime($membre['lastAboEnd']) : false;

    // « 5,5 » / « 1 140 » : décimale seulement quand il y en a une.
    $nombre = function ($valeur) {
        return number_format_i18n($valeur, fmod($valeur, 1) == 0 ? 0 : 1);
    };

    $tuiles = [
        [
            'valeur' => $nombre($balance),
            'label'  => $balance < 0
                ? 'solde de tickets à régulariser'
                : ($balance < 2 ? 'ticket disponible' : 'tickets disponibles'),
            'alerte' => $balance < 0,
        ],
        [
            'valeur' => $fin_abo ? date_i18n('j M Y', $fin_abo) : 'Aucun',
            'label'  => $fin_abo ? 'fin de votre abonnement' : 'abonnement en cours',
            'alerte' => false,
        ],
        [
            'valeur' => $nombre($presences),
            'label'  => 'journées de présence au total',
            'alerte' => false,
        ],
    ];
    ?>
    <div class="solde-membre">
        <div class="solde-membre__grille">
            <?php foreach ($tuiles as $tuile) : ?>
                <div class="solde-membre__tuile<?php echo $tuile['alerte'] ? ' solde-membre__tuile--alerte' : ''; ?>">
                    <span class="solde-membre__valeur"><?php echo esc_html($tuile['valeur']); ?></span>
                    <span class="solde-membre__label"><?php echo esc_html($tuile['label']); ?></span>
                </div>
            <?php endforeach; ?>

            <div class="solde-membre__tuile solde-membre__rappel">
                <p class="solde-membre__rappel-texte">Vérifiez votre solde avant tout achat.</p>
                <a class="solde-membre__rappel-lien" href="<?php echo esc_url(home_url('/mon-compte/')); ?>">
                    Voir le détail
                </a>
            </div>
        </div>

        <?php if ($balance < 0) : ?>
            <p class="solde-membre__alerte">
                <span class="solde-membre__alerte-icone" aria-hidden="true">⚠</span>
                <span><strong>Solde négatif :</strong> l&rsquo;accès à l&rsquo;espace est conditionné à un solde
                    positif. Merci de régulariser avec un carnet de journées ou un ticket à l&rsquo;unité.</span>
            </p>
        <?php endif; ?>
    </div>
    <?php
}
