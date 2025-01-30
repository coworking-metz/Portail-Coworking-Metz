<?php


$current_user = wp_get_current_user();
if($current_user) {
	echo $full_name = trim( $current_user->first_name . ' ' . $current_user->last_name );
}