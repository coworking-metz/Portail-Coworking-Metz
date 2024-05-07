<?php


$files = glob(__DIR__.'/plugins/*.php');
$files = apply_filters('custom-mu-plugins',$files);
foreach($files as $file) {
	require $file;
}