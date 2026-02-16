<?php



if (isset($_GET['export-stats'])) {
    add_action('admin_init', function () {

		$cache=true;
        $annee = $_GET['annee'] ?? date('Y') - 1;
        if ($annee == 'current') {
			$cache=false;
            $annee = date('Y');
        }
        $limit = intval($_GET['limit'] ?? 70);
        $members = tickets('/members');


		$all_members = [];
        $curation = [];
        foreach ($members as $member) {
            $lastSeen = explode('-', $member['lastSeen']??'')[0] ?? false;
            if ($lastSeen < $annee) continue;
            $all_members[] = $member;
        }

        foreach ($all_members as $member) {
            $uid = $member['wpUserId'];
            $jours = get_jours_presence_par_annee($member, $annee);

            if ($jours > $limit) {
                $member['statut_juridique'] = get_field('statut_juridique', 'user_' . $uid);
                $member['type_activite'] = get_field('type_activite', 'user_' . $uid);
				$member['jours']=$jours;
				$curation[] = $member;
            }
        }

		usort($curation, fn ($a, $b) => $b['jours'] <=> $a['jours']);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=coworking-stats-' . $annee . '-' . wp_date('Y-m-d-H-i-s') . '.csv');

        $output = fopen('php://output', 'w');

        $first = $curation[0];
        unset($first['_id']);
        unset($first['wpUserId']);
        unset($first['thumbnail']);
        unset($first['activeSubscriptions']);
        $keys = array_keys($first);
        fputcsv($output, $keys);


        foreach ($curation as $idx => $member) {

            $line = [];
            foreach ($keys as $key) {
                $line[$key] = $member[$key] ?? '';
            }

            fputcsv($output, $line);
        }
        fclose($output);
        exit;
    });
}
