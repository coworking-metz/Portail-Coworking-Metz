<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {

        $i=$_GET['i'];

        echo '<img src="'.$i.'" width=300>';
        me(isImagePhotoConnue($i));
        exit;
    });
}
