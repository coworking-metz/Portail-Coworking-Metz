<?php

/**
 * picture_user_presence();
 * Get the picture coworker
 */

function picture_user_presence()
{

    $hour_now = date('H');
    $hour_now += 1;
    $start_hour = 10;
    $end_hour = 14;


    if ($hour_now >= $start_hour && $hour_now < $end_hour) {
        $url = 'https://tickets.coworking-metz.fr/api/current-users?key=' . API_KEY_TICKET . '&delay=180';
    } else {
        $url = 'https://tickets.coworking-metz.fr/api/current-users?key=' . API_KEY_TICKET . '&delay=15';
    }

    $data = file_get_contents($url);
    $json = json_decode($data, true);

    //DIV wrapper
    echo '<div class="grid-images">';

    //variables si au moins une personne est en négatif en tickets ou n'a pas sa carte de membre, pour afficher le message

    echo '<link rel="stylesheet" type="text/css" href="/fonts/andalusia/stylesheet.css">';
?>
    <style>
        .animated-image {
            position: relative;
        }

        .animated-image span {
            position: absolute;
            bottom: 0;
            width: 100%;
            left: 0;
            height: 18%;
            font-family: 'andalusia';
            text-align: center;
            font-size: 2em;
        }
    </style>
<?php
    //Images trombi
    foreach ($json as $key => $value) {
        $i = 0;
        // $url_image = get_user_meta($value['wpUserId'], $key = 'url_image_trombinoscope', $single = true);
        // $image_array = wp_get_attachment_image_src($url_image);
        // $image_url = $image_array[0];
        $user_balance = $value['balance'];
        $membership_Ok = $value['membershipOk'];

        $random_1 = rand(-6, 6);
        $random_2 = rand(0, 1000) + ($i * 100);

        // tickets ou carte à jour ? sinon on ajoute la classe wanted
        $wanted_membership = $membership_Ok ? '' : ' wanted';
        $wanted_tickets = $user_balance >= 0 ? '' : ' wanted';

        // if (!$image_url) {
        //     $imgPath = '/images/pola-poule-vide.jpg';
        //     $code = '<img src="' . $imgPath . '" />';
        //     $name = trim(($value['firstName'] ?? '') . ' ' . ($value['lastName'] ?? ''));
        //     $code .= '<span>' . $name . '</span>';
        // } else {
        //     $code = '<img src="' . $image_url . '" />';
        // }
        $code = '<img src="/polaroid/' . $value['wpUserId'] . '.jpg" />';

        echo '<div class="animated-image' . $wanted_membership . $wanted_tickets . '" style="transform: rotate(' . $random_1 . 'deg); animation-delay: ' . $random_2 . 'ms;">';
        //      echo '<div class="test">' . $membership_Ok . '</div>';
        echo $code;
        echo '</div>';

        $i++;
    }
    //DIV wrapper closing
    echo '</div>';

    echo '<div class="legend"><img class="img-legend" src="https://www.coworking-metz.fr/wp-content/themes/ave-child/img/ninja.png" _width="128px"/> : Adhésion ou solde de tickets négatif. </div>';
}
