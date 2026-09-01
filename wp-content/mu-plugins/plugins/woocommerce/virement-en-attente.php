<?php
/**
 * Rappel des coordonnées bancaires sur une commande en attente de virement.
 *
 * Sur « Mon compte > Voir la commande », si la commande a été payée par virement
 * (passerelle BACS) et qu'elle n'est pas encore réglée, on réaffiche en bas de page
 * les instructions et l'IBAN configurés dans WooCommerce > Paiements > Virement
 * bancaire. L'affichage est délégué à la passerelle elle-même
 * (WC_Gateway_BACS::thankyou_page) pour que l'IBAN n'existe qu'à un seul endroit.
 */

add_action('woocommerce_view_order', function ($order_id) {

    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }

    // Uniquement les commandes réglées par virement et encore impayées.
    if ('bacs' !== $order->get_payment_method()) {
        return;
    }

    if (!$order->has_status(['on-hold', 'pending', 'failed'])) {
        return;
    }

    $gateways = WC()->payment_gateways()->payment_gateways();
    $bacs = $gateways['bacs'] ?? null;
    if (!$bacs || !method_exists($bacs, 'thankyou_page')) {
        return;
    }

    ?>
    <section class="woocommerce-order-bacs-reminder">
        <h2><?php esc_html_e('Régler cette commande par virement', 'woocommerce'); ?></h2>
        <p>
            <?php
            printf(
                'Cette commande est en attente de votre virement de %1$s. Merci d\'indiquer la référence %2$s dans le libellé du virement.',
                '<strong>' . wp_kses_post($order->get_formatted_order_total()) . '</strong>',
                '<strong>' . esc_html($order->get_order_number()) . '</strong>'
            );
            ?>
        </p>
        <?php $bacs->thankyou_page($order_id); ?>
    </section>
    <?php
}, 20);
