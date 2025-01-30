<?php



if (isset($_GET['export-prospects'])) {
    add_action('admin_init', function () {
		global $wpdb;
        $annee = $_GET['annee'] ?? date('Y');
        $orders= $_GET['orders'] ?? 0;
	
			if($annee=='previous') {
				$annee = date('Y')-1;
			}

    $users = get_users([
        'meta_key' => 'visite',
    ]);

	$users = array_filter($users, function($user) use ($annee){
		if(strtotime($user->visite) > time()) return false;
		return strstr($user->visite, $annee);
	});


	foreach($users as &$user) {
			$nb_orders = get_user_order_count($user->ID);
			if($nb_orders <= $orders) {
				$user->data->nb_orders = $nb_orders;
				$user->data->trialDay = $user->visite;
			} else {
				$user=false;
			}
	}
	$users = array_filter($users);

		// Generate CSV file
		$filename = 'exported_prospects_' . $annee. '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="' . $filename . '"');

		$output = fopen('php://output', 'w');

		// Add CSV headers
		fputcsv($output, ['User ID', 'Email', 'Display Name', 'Visite', 'Commandes']);

		// Add user data
		foreach ($users as $user) {
			fputcsv($output, [
				$user->ID,
				$user->user_email,
				$user->display_name,
				$user->visite,
				$user->data->nb_orders,
			]);
		}

		fclose($output);
		exit;

    });
}
