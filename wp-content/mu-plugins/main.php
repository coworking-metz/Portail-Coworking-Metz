<?php
/*
Plugin Name: MU Plugin Coworking
Description: DIfférents outils et extensions pour le site
Author: GF
Version: 1.0
*/


define('MUDIR', ABSPATH.'/wp-content/mu-plugins');

include MUDIR.'/Classes/CloudFlare.php';

include MUDIR.'/coworking-app/app.php';

include MUDIR.'/colonnes/colonnes.php';
include MUDIR.'/mon-compte/mon-compte.php';
include MUDIR.'/polaroid/polaroid.php';
include MUDIR.'/notifications/notifications.php';

// Récupérer tous les fichiers .inc.php dans le dossier ./includes en utilisant __DIR__
foreach (glob(MUDIR . "/includes/*.inc.php") as $filename) {
    include $filename;
}

// Récupérer tous les fichiers .inc.php dans le dossier ./includes en utilisant __DIR__
foreach (glob(MUDIR . "/cli/*.cli.php") as $filename) {
    include $filename;
}

add_action('admin_init',function() {

    // Ajouter les fichiers js
    foreach (glob(MUDIR . "/js/*.js") as $filename) {
        ajouter_js(explode('.',basename($filename))[0]);
    }
    
    // Ajouter les fichiers css
    foreach (glob(MUDIR . "/css/*.css") as $filename) {
        ajouter_css(explode('.',basename($filename))[0]);
    }
});

add_action('init',function() {


    // Ajouter les fichiers js
    foreach (glob(__DIR__ . "/js/front/*.js") as $filename) {
        ajouter_js('front/'.explode('.',basename($filename))[0]);
    }
    
    // Ajouter les fichiers css
    foreach (glob(__DIR__ . "/css/front/*.css") as $filename) {
        ajouter_css('front/'.explode('.',basename($filename))[0]);
    }
    
    $uri = $_SERVER['REQUEST_URI']??"";
    foreach(glob(__DIR__ . "/js/front/**/*.js") as $filename) {
        $fileKey = explode('.',basename($filename))[0];
        $folder = getLastFolderName($filename);
        if(strstr($uri,$folder)) {
            ajouter_js('front/'.$folder.'/'.$fileKey);
        }
    }

    foreach(glob(__DIR__ . "/css/front/**/*.css") as $filename) {
        $fileKey = explode('.',basename($filename))[0];
        $folder = getLastFolderName($filename);
        if(strstr($uri,$folder)) {
            ajouter_css('front/'.$folder.'/'.$fileKey);
        }
    }

});

$files = glob(__DIR__.'/plugins/*.php');
$files = array_merge($files, glob(__DIR__.'/plugins/**/*.php'));
$files = apply_filters('custom-mu-plugins',$files);
foreach($files as $file) {
	require $file;
}
