<?php

define('COULEUR_PRINCIPALE', '6db992');
define('COULEUR_PRINCIPALE_ALT', '146d71');
add_action('wp_head', function () {
?>
    <script>
        const COULEUR_PRINCIPALE = "#<?= COULEUR_PRINCIPALE; ?>";
    </script>
    <style>
        :root {
            --couleur-principale: #<?= COULEUR_PRINCIPALE; ?>;
            --couleur-principale-alt: #<?= COULEUR_PRINCIPALE_ALT; ?>;
        }
    </style>
<?php
});

if (isset($_GET['change_theme_color'])) {
    add_action('init', function () {
        if (!is_admin()) return;
        global $wpdb;
        $color= $_GET['color'] ?? false;
		if(!$color) return;
        $previous_color = $_GET['previous_color'] ?? false;
		if(!$previous_color) return;
		$dryRun = true;
		m($_GET);
		if(isset($_GET['ok'])) {
			$dryRun=false;
		}
        // Fetch all options
        $options = $wpdb->get_results("SELECT option_id, option_value, option_name FROM $wpdb->options");
        $changed = [];
        foreach ($options as $option) {
            // Check if the option_value contains the search string
            if (stripos($option->option_value, $previous_color) !== false) {
                // Replace occurrences of the search string
                if (!stristr($option->option_value, $previous_color)) continue;
                $changed[] = 'option:'.$option->option_id.':'.$option->option_name;
				if($dryRun) continue;
                $updated_value = str_ireplace($previous_color, $color, $option->option_value);

                // Update the option in the database
                $wpdb->update(
                    $wpdb->options,
                    ['option_value' => $updated_value],
                    ['option_id' => $option->option_id]
                );
            }
        }

        $postmetas= $wpdb->get_results("SELECT * FROM $wpdb->postmeta");
        $changed = [];
        foreach ($postmetas as $postmeta) {
            // Check if the meta_value contains the search string
            if (stripos($postmeta->meta_value, $previous_color) !== false) {
                // Replace occurrences of the search string
                if (!stristr($postmeta->meta_value, $previous_color)) continue;
                $changed[] = 'postmeta:'.$postmeta->meta_id.':'.$postmeta->meta_key;
				if($dryRun) continue;
                $updated_value = str_ireplace($previous_color, $color, $postmeta->meta_value);

                // Update the option in the database
                $wpdb->update(
                    $wpdb->options,
                    ['meta_value' => $updated_value],
                    ['meta_id' => $postmeta->meta_id]
                );
            }
        }

        $posts= $wpdb->get_results("SELECT * FROM $wpdb->posts");
        $changed = [];
        foreach ($posts as $post) {
            // Check if the post_content contains the search string
            if (stripos($post->post_content, $previous_color) !== false) {
                // Replace occurrences of the search string
                if (!stristr($post->post_content, $previous_color)) continue;
                $changed[] = 'post:'.$post->ID.':'.$post->post_title;
				if($dryRun) continue;
                $updated_value = str_ireplace($previous_color, $color, $post->post_content);

                // Update the option in the database
                $wpdb->update(
                    $wpdb->options,
                    ['post_content' => $updated_value],
                    ['ID' => $post->ID]
                );
            }
        }

	if($dryRun) {
		m('Dry run, add ok to the query to execute');
	}
        me($changed);
    });
}
