<?php

add_action('rest_api_init', function () {
    register_rest_route('cowo/v1', '/visites', array(
        'methods' => 'GET',
        'permission_callback' => function () {
            return current_user_can('administrator') || isset($_GET['open']);
        },
        'callback' => function ($data) {
            $users = fetch_users_with_future_visite();
            $payload = [];
            foreach($users as $user) {
                $data = [];
                $data['wpUserId']=$user->ID;
                $data['email']=$user->user_email;
                $data['name']=$user->display_name;
                $data['visite']=date('Y-m-d', strtotime($user->visite));
                $data['activite']=$user->activite;
                $payload[]=$data;
            }
            return $payload;
        }
    ));
    
});

add_action('rest_api_init', function () {
    register_rest_route('cowo/v1', '/visites/today', array(
        'methods' => 'GET',
        'permission_callback' => function () {
            return current_user_can('administrator') || isset($_GET['open']);
        },
        'callback' => function ($data) {
            $users = fetch_users_with_visite_today();
            $payload = [];
            foreach($users as $user) {
                $data = [];
                $data['wpUserId']=$user->ID;
                $data['email']=$user->user_email;
                $data['name']=$user->display_name;
                $data['visite']=date('Y-m-d', strtotime($user->visite));
                $data['activite']=$user->activite;
                $payload[]=$data;
            }
            return $payload;
        }
    ));
    
});

