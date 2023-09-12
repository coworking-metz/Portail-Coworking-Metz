<?php

/**
 * picture_user_presence();
 * Get the picture coworker
 */

function picture_user_presence()
{

?>
    <style>
        h3 {
            font-weight: 300;
        }

        #title-presents {
            transform: translateX(-5%);
            color: #fff;
        }

        #logo-poulailler {
            width: 75%;
        }

        .legend {
            color: #eab234;
            text-align: center;
            padding-top: 15px;
            font-size: 4em;
        }

        .img-legend {
            position: relative;
            /*top: -50px;*/

        }

        .header-page {
            /*max-height: 125px;*/
        }

        .highlight-text {
            color: #eab234;
            font-weight: 700;
        }

        .grid-images {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 200px);
            margin-top: -30px;
        }


        .grid-images .animated-image {
            position: relative;
            /* animation-name: zoomIn;
            animation-duration: 1.8s;
            animation-iteration-count: 1;
            animation-timing-function: ease-out;
            animation-fill-mode: both;
            animation-direction: both; */
            z-index: 1;
            width: 10.5%;
            margin: 10px;
        }


        .grid-images .animated-image img {
            display: block;
            width: 100%;
            height: auto;
        }

        .grid-images .animated-image.wanted:after {
            background-color: rgba(255, 255, 255, 0.5);
            border: .3vw solid red;
            display: block;
            content: '';
            position: absolute;
            inset: 0;
            /* background: url('/wp-content/themes/ave-child/img/wanted.png') no-repeat 0 0/contain; */
            z-index: 2;
        }


        /* animation */

        @-webkit-keyframes zoomIn {
            from {
                opacity: 0;
                -webkit-transform: scale3d(.3, .3, .3);
                transform: scale3d(.3, .3, .3)
            }

            50% {
                opacity: 1;
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                -webkit-transform: scale3d(.3, .3, .3);
                transform: scale3d(.3, .3, .3);
            }

            50% {
                opacity: 1;
            }
        }

        .animated-image {
            -webkit-backface-visibility: hidden;
            outline: 1px solid transparent;
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

        .vc_custom_1634288937073 {
            /* background: none !important; */
            position: relative;
        }

        .vc_custom_1634288937073:after {
            filter: blur(20px);
            position: absolute;
            inset: 0;
            content:"";
            background-size: cover;
            /* background-image: url(https://www.coworking-metz.fr/wp-content/uploads/2023/09/slide-poulailler1.jpg) !important; */
        }
    </style>
    <?php
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
        $code = '<img src="/polaroid/' . $value['wpUserId'] . '.jpg?1" />';

        echo '<a href="/wp-admin/user-edit.php?user_id=' . $value['wpUserId'] . '" class="animated-image' . $wanted_membership . $wanted_tickets . '" _style="transform: rotate(' . $random_1 . 'deg); animation-delay: ' . $random_2 . 'ms;">';
        //      echo '<div class="test">' . $membership_Ok . '</div>';
        echo $code;
        echo '</a>';

        $i++;
    }
    //DIV wrapper closing
    echo '</div>';
    echo '<div class="legend" style="position:fixed;left:20px;bottom:20px;font-size:20px">Choisissez votre polaroïd sur le site du coworking, dans le menu "Mon compte"</div>';
    // echo '<div class="legend"><img class="img-legend" src="https://www.coworking-metz.fr/wp-content/themes/ave-child/img/ninja.png" _width="128px"/> : Adhésion ou solde de tickets négatif. </div>';
}
