<?php



add_action('woocommerce_edit_account_form', function () {
    $user_id = get_current_user_id();
    $date_naissance = get_user_meta($user_id, 'date_naissance', true);
?>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="date_naissance">Date de naissance</label>
        <input type="date" class="woocommerce-Input woocommerce-Input--text input-text" name="date_naissance" id="date_naissance" value="<?php echo esc_attr($date_naissance); ?>" pattern="\d{2}/\d{2}/\d{4}" />
    </p>
<?php });



add_action('woocommerce_save_account_details', function ($user_id) {
    if (isset($_POST['date_naissance'])) {
        update_user_meta($user_id, 'date_naissance', sanitize_text_field($_POST['date_naissance']));
    }
});



add_action('woocommerce_after_checkout_billing_form', function ($checkout) {
    if ($user_id = get_current_user_id()) {
        woocommerce_form_field('date_naissance', array(
            'type'          => 'date',
            'class'         => array('form-row-wide'),
            'label'         => 'Date de naissance',
            'placeholder'   => '',
            'required'      => true,
            'custom_attributes' => ['required' => true]
        ), $checkout->get_value('date_naissance'));
    }
});


add_action('woocommerce_checkout_process', function () {
    if ($user_id = get_current_user_id()) {
        // Check if set, if its not set add an error.
        if (!$_POST['date_naissance']) {
            wc_add_notice('Merci de renseigner votre date de naissance', 'error');
        }
    }
});



add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
    if (isset($_POST['date_naissance'])) {
        if ($user_id = get_current_user_id()) {
            update_user_meta($user_id, 'date_naissance', sanitize_text_field($_POST['date_naissance']));
        }
    }
});
