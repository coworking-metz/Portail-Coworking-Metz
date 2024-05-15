<?php



if (isset($_GET['export-stats'])) {
    add_action('admin_init', function () {

        $annee = $_GET['annee'] ?? date('Y') - 1;
        $limit = $_GET['limit'] ?? 70;
        $members = get_json(TICKET_BASE_URL . '/members?key=' . API_KEY_TICKET);

        $curation = [];
        foreach ($members as $member) {
            $lastSeen = explode('-', $member['lastSeen'])[0] ?? false;
            if ($lastSeen < $annee) continue;
            $curation[] = $member;
        }

        foreach ($curation as &$member) {
            $uid = $member['wpUserId'];
            $activity = get_json(TICKET_BASE_URL . '/members/' . $uid . '/activity?key=' . API_KEY_TICKET);
            $jours = 0;
            foreach ($activity as $presence) {
                if (!isWorkDay($presence['date'])) continue;
                if (strstr($presence['date'], $annee)) {
                    $jours += $presence['value'];
                }
            }
            $member['jours'] = $jours;
            if ($jours > $limit) {
                $member['statut_juridique'] = get_field('statut_juridique', 'user_' . $uid);
                $member['type_activite'] = get_field('type_activite', 'user_' . $uid);
            }
        }

        $curation = array_filter($curation, function ($item) use ($limit) {
            return $item['jours'] > $limit;
        });
        // Sort the filtered array by the 'jours' value
        usort($curation, function ($a, $b) {
            return $a['jours'] <=> $b['jours'];
        });
        $curation = array_reverse($curation);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=coworking-stats-' . $annee . '-' . wp_date('Y-m-d-H-i-s') . '.csv');

        $output = fopen('php://output', 'w');

        $first = $curation[0];
        unset($first['_id']);
        unset($first['wpUserId']);
        unset($first['thumbnail']);
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
