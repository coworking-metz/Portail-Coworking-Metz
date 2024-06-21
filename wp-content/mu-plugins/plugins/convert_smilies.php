<?php

add_action('init', function() {
    // Remove the convert_smilies filter from the_content
    remove_filter('the_content', 'convert_smilies', 20);
    // Remove the convert_smilies filter from the_excerpt
    remove_filter('the_excerpt', 'convert_smilies', 20);
    // Remove the convert_smilies filter from comment_text
    remove_filter('comment_text', 'convert_smilies', 20);
});
