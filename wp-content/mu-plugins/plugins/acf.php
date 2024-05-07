<?php


add_filter('the_content', function ($content) {
    // Get the global post object
    global $post;
    if ($post->post_type != 'page') return $content;

    // Get the meta value for 'importer_depuis_github'
    $importer_depuis_github = get_field('importer_depuis_github', $post->ID);
    
    // Check if the meta value is not empty
    if (!empty($importer_depuis_github)) {
        $depuis_github = get_field('depuis_github', $post->ID);
        if ($depuis_github['url'] ?? false) {
            $url = $depuis_github['url'];
            $branch = sanitize_title($_GET['branch']??false);
            if($branch) {
                $url = str_replace('master/',$branch.'/', $url);
            }
            return '<div class="container">'.fetchAndParseMarkdown($url).'</div>';
        }
    }

    return $content;
}, 1);
