<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {
        $users = json_decode(file_get_contents('https://tickets.coworking-metz.fr/api/users-stats?key=bupNanriCit1&period=all-time'), true);

        foreach($users as &$user) {

            if($user['presencesJours'] < 25) $user = false;
            if(str_replace('-','',$user['createdAt']) > '20230701') { 
                $user = false;
            } 
            
            if($user) {
                $user['last']=get_user_meta($user['wpUserId'], '_derniere_activite', true);

                if(!$user['last']) 
                $user=false;


                if(str_replace('-','',$user['last']) > '20230701') { 
                    $user = false;
                } 
            }
        }
        header('Content-type: application/json');
        echo json_encode(array_filter($users));
        exit;
    });
}
