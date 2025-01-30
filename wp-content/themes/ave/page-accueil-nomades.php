<?php
/**
 * The template for displaying pages
 *
 * @package Ave theme
 */

CoworkingMetz\Cloudflare::noCacheHeaders();
// Redirect non-logged-in users to the login page
if ( ! is_user_logged_in() ) {
    wp_redirect( site_url( '/mon-compte/?redirect_to=' ) . urlencode( $_SERVER['REQUEST_URI'] ) );
    exit;
}

if(isset($_GET['nomade-presence'])) {
	$user_id = get_current_user_id();

	$mac = $_GET['mac']??false;
	addMemberMacAddress($user_id, $mac);

	wp_redirect_notification('/accueil-nomades/',[
		'id'=>time(),
        'type' => 'success',
        'titre' => 'Identification réussie',
        'texte' => 'Votre appareil a été identifié par notre système. <b>Vous pouvez commencer votre journée de coworking nomade !</b><br>Une question, un problème ? <a href="#ouvrir-brevo">Contacter nous ici</a>.',
    ]);
}

if(isset($_GET['ping'])) {
	?>
<script>
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 2000);

        fetch('https://probe.coworking-metz.fr/info', {
            cache: 'no-cache',
            signal: controller.signal
        })
            .then(response => response.json())
            .then(data => {
                
                console.log({ data })
				const device = data?.device;
				if(device) {
				const mac = device?.macAddress
					if(mac) {
						return window.open('/accueil-nomades/?nomade-presence&mac='+mac,'_self');
					}
				}
			
				window.open('/accueil-nomades/?nomade-error=unknown','_self');
					
			})
          .catch(error => {
				window.open('/accueil-nomades/?nomade-error=nowifi','_self');
            });

</script>
	<?php
		
exit;
}
get_header();

$error = $_GET['nomade-error']??false;

if($error == 'nowifi') {
    echo generateNotification([
		'id'=>time(),
        'type' => 'warning',
        'titre' => 'Mauvais réseau Wifi',
        'texte' => 'Votre appareil doît être connecté au réseau <b>Wifi-du-Poulailler</b> pour effectuer cette opération. Connectez-vous à ce réseau avec le mot de passe <code>Poulailler</code> avant de recommencer.',
		'fermer'=>false,
        'cta' => [
            'url' => '/accueil-nomade/?ping=true',
            'caption' => 'Recommencer la procédure'
        ],
        // 'image' => '/images/cafe.jpg'
    ]);

} else if($error) {
    echo generateNotification([
		'id'=>time(),
        'type' => 'warning',
        'titre' => 'Erreur',
        'texte' => 'Nous n\'avons pas réussi à identifier votre appareil. Veuillez essayer à nouveau, et si le problème persiste, <a href="#ouvrir-brevo">contacter nous ici</a>',
		'fermer'=>false,
        'cta' => [
            'url' => '/accueil-nomade/?ping=true',
            'caption' => 'Recommencer la procédure'
        ],
        // 'image' => '/images/cafe.jpg'
    ]);

}


?>
<style>.titlebar-inner {display:none !important;}</style>
<?php
while ( have_posts() ) : the_post();

    liquid_get_content_template();

endwhile;

get_footer();
