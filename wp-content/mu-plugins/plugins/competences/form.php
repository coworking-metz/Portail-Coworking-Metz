<?php



function competences_form() {
	$user_id = get_current_user_id();
	$optin = get_user_meta($user_id, 'annuaire_optin', true);
	$competences = get_user_meta($user_id, 'annuaire_competences', true);
	$description = get_user_meta($user_id, 'annuaire_description', true);

	$contact = get_user_meta($user_id, 'annuaire_contact', true) ?: get_userdata($user_id)->user_email ?? '';

	?>
		<script><?=file_get_contents(__DIR__.'/competences.js');?></script>		<style><?=file_get_contents(__DIR__.'/competences.css');?></style>
<form class="woocommerce-EditAccountForm edit-account edit-competences" action="" method="post">

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="annuaire_optin">
				<input type="checkbox" name="annuaire_optin" id="annuaire_optin" value="1" <?php checked($optin, '1'); ?>>
				<strong>J'accepte de figurer dans l’annuaire des compétences</strong>
			</label>
			<span>Si vous cochez cette case, vous consentez à apparaître dans l'annuaire réservé aux adhérent(e)s et d'être démarché</span>
		</p>

		<div class="details-competences">
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="annuaire_competences">Adresse de contact</label>
			<input type="email" class="woocommerce-Input input-text" name="annuaire_contact" id="annuaire_contact" value="<?php echo esc_attr($contact); ?>">
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="annuaire_competences">Compétences</label>
			<input type="text" class="woocommerce-Input input-text" name="annuaire_competences" id="annuaire_competences" value="<?php echo esc_attr($competences); ?>">
			<span>Un résumé de vos compétences en quelques mots. Exemple: <code>Marketing, comptabilité et boulangerie</code>
		</p>

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="annuaire_description">Description</label>
			<textarea name="annuaire_description" id="annuaire_description" rows="4" class="woocommerce-Input input-text"><?php echo esc_textarea($description); ?></textarea>
		</p>
		</div>

		<p><button type="submit" class="woocommerce-Button button" name="save_account_details" value="Enregistrer les modifications">Enregistrer les modifications</button> <input type="hidden" name="action" value="save_competences"> </p>

	</form>
	<?php

}

if(($_POST['action']??'') === 'save_competences') {
	add_action('init', function() {
		$user_id = get_current_user_id();
		update_user_meta($user_id, 'annuaire_optin', isset($_POST['annuaire_optin']) ? '1' : '0');
		if (isset($_POST['annuaire_competences']))
			update_user_meta($user_id, 'annuaire_competences', sanitize_text_field($_POST['annuaire_competences']));
		if (isset($_POST['annuaire_contact']))
			update_user_meta($user_id, 'annuaire_contact', sanitize_text_field($_POST['annuaire_contact']));
		if (isset($_POST['annuaire_description']))
			update_user_meta($user_id, 'annuaire_description', wp_kses_post($_POST['annuaire_description']));
		wp_redirect('/mon-compte/competences/');
		exit;
	});
}