<?php

// if (isset($_GET['test'])) {
//     $_GET['debug'] = true;
//     add_action('init', function () {
//         global $wpdb;
//         $table_name = $wpdb->prefix . 'phppc_functions';
//         $query = "SELECT * FROM {$table_name}";
//         $results = $wpdb->get_results($query);
//         foreach($results as $item) {
//             $content = base64_decode($item->code);
//             $slug = $item->slug;
//             if(!$slug) {
//                 $slug = sanitize_title($item->name);
//             } 
//             if(!$slug) {
//                 $slug = 'php-shortcode';
//             }

            
//             if(!$slug) me($slug, $item);
//             $slug.='--'.$item->id;

//             $file = ABSPATH.MUPLUGINDIR.'/php-shortcodes/'.$slug.'.php';
//             $content = replace_first_occurrence('<?php','<?php'.PHP_EOL.'// '.$item->id.' - '.$item->name.PHP_EOL, $content);
//             file_put_contents($file, $content);
//             // m($item, $file);
//         }
//         exit;

//     });
// }
