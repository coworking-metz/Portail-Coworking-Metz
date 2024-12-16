<?php




if(strstr(explode('?',$_SERVER['REQUEST_URI']??'')[0], 'mot-de-passe-du-wifi')) {
	add_action('init',function() {
		\CoworkingMetz\CloudFlare::noCacheHeaders();

		if ( ! is_user_logged_in() ) {
			wp_redirect( '/mon-compte/?redirect_to=/mot-de-passe-du-wifi/'  );
			exit;
		}
	});
}
