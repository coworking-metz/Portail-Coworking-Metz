<?php



add_action('woocommerce_after_checkout_billing_form', function ($checkout) {
    if ($user_id = get_current_user_id()) {

        $field = acf_get_field('statut_juridique');
        $statut_juridique = get_field('statut_juridique', 'user_' . $user_id);
        woocommerce_form_field('statut_juridique', array(
            'type'          => 'select',
            'input_class'   => array('input-text'),
            'label'         => 'Votre statut juridique',
            'options' => [''] + $field['choices'],
            'required'      => true,
            'custom_attributes' => ['required' => true]
        ), $statut_juridique);


        $field = acf_get_field('type_activite');
        $type_activite = get_field('type_activite', 'user_' . $user_id);
        woocommerce_form_field('type_activite', array(
            'type'          => 'select',
            'input_class'   => array('input-text'),
            'label'         => 'Type d\'activité',
            'options' => [''] + $field['choices'],
            'required'      => true,
            'custom_attributes' => ['required' => true]
        ), $type_activite);
    }
});


add_action('woocommerce_checkout_process', function () {
    if ($user_id = get_current_user_id()) {
        if (!$_POST['statut_juridique']) {
            wc_add_notice('Merci de renseigner votre statut juridique', 'error');
        }
        if (!$_POST['type_activite']) {
            wc_add_notice('Merci de renseigner votre type d\'activité', 'error');
        }
    }
});


add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
    if ($user_id = get_current_user_id()) {
        if (isset($_POST['statut_juridique'])) {
            update_field('statut_juridique', $_POST['statut_juridique'], 'user_' . $user_id);
        }
        if (isset($_POST['type_activite'])) {
            update_field('type_activite', $_POST['type_activite'], 'user_' . $user_id);
        }
    }
});
