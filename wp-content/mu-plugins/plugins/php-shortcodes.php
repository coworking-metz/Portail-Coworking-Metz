<?php

if (isset($_GET['chercher-shortcode'])) {
    add_action('init', function () {
        global $wpdb;
        $search_string = $_GET['chercher-shortcode'] ? $_GET['chercher-shortcode'] : '[php ';

        // Fetch all tables from the database
        $tables = $wpdb->get_col("SHOW TABLES");

        foreach ($tables as $table) {
            // Check if the columns are stored in a transient
            $transient_key = 'columns_' . $table;
            $columns = get_transient($transient_key);

            if (!$columns) {
                // Fetch columns with text-based data types
                $columns = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table' AND DATA_TYPE IN ('char', 'varchar', 'text', 'mediumtext', 'longtext')", ARRAY_A);
                // Store the result in a transient for future use
                set_transient($transient_key, $columns, DAY_IN_SECONDS); // Adjust the time as necessary
            }

            foreach ($columns as $column) {
                $column_name = $column['COLUMN_NAME'];
                // Construct the SQL query to search for the search string in each column
                $query = $wpdb->prepare("SELECT * FROM $table WHERE $column_name LIKE %s", '%' . $wpdb->esc_like($search_string) . '%');
                $results = $wpdb->get_results($query);

                // Output results
                if (!empty($results)) {
                    echo "<hr><h2>Found in $table ($column_name): </h2>";
                    foreach ($results as $result) {
                        $key = array_keys((array)$result)[0];
                        if ($table == 'wp_posts') {
                            if ($result->post_status != 'publish') continue;
                            echo '<h3><a target=_blank href="https://www.coworking-metz.fr/wp-admin/post.php?post=' . $result->$key . '&action=edit">' . $key . '=' . ($result->$key) . '</a> ' . $result->post_status . ' - ' . $result->post_type . '</h3>';
                        } else {
                            echo '<h3>' . $key . '=' . ($result->$key) . '</h3>';
                        }
                        $content = $result->$column_name;

                        // Find the position of the search string in the content
                        $position = strpos($content, $search_string);
                        if ($position !== false) {
                            // Calculate the start position for the substring
                            $start = max(0, $position - 30);
                            // Calculate the length of the substring to take
                            $length = strlen($search_string) + 70;
                            // Extract the relevant part of the content
                            $extracted_content = substr($content, $start, $length);
                            // Replace the search string within this extracted content
                            $highlighted_content = str_replace($search_string, '<strong style="color:red">' . $search_string . '</strong>', htmlspecialchars($extracted_content));
                            // Display the highlighted content within a <code> tag
                            echo '<code>' . $highlighted_content . '</code>';
                        } else {
                            // Display the content as is if the search string is not found
                            echo '<code>' . htmlspecialchars($content) . '</code>';
                        }
                    }
                }
            }
        }
        exit;
    });
}

add_action('init', function () {
    add_shortcode('php', function ($atts = [], $content = null) {
        $atts = shortcode_atts([
            'slug' => '',
            'id' => '',
            'snippet' => ''
        ], $atts, 'php');

        file_get_contents('https://coworking.requestcatcher.com/phpshortcode/legacy?' . urldecode(http_build_query($atts)));
    });
    add_shortcode('phpcode', function ($atts = [], $content = null) {
        // Shortcode attributes with default values
        $atts = shortcode_atts([
            'slug' => '',
            'id' => '',
            'snippet' => ''
        ], $atts, 'phpcode');

        $id = $atts['snippet'] ?? $id ?? false;
        $file = false;
        if ($atts['slug']) {
            $file = glob(ABSPATH . MUPLUGINDIR . '/php-shortcodes/' . $atts['slug'] . '--*.php')[0] ?? false;
            if(!$file) {
                $file = glob(ABSPATH . MUPLUGINDIR . '/php-shortcodes/' . $atts['slug'] . '.php')[0] ?? false;
            }
        } else if ($id) {
            $file = glob(ABSPATH . MUPLUGINDIR . '/php-shortcodes/' . '*--' . $id . '.php')[0] ?? false;
        }
        if (!$file) {
            file_get_contents('https://coworking.requestcatcher.com/phpshortcode/ko?' . urldecode(http_build_query($atts)));

            return;
        }

        ob_start();
        include $file;
        $content = ob_get_clean();

        return $content;
    });
});
