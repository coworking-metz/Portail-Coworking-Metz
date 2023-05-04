<?php
/**
 * api_top_twenty();
 * Get the top twenty list of presences from the ticket service
 */
function api_top_twenty_challenge() {

    $curl = curl_init();

    $yesterday = date('Y-m-d',strtotime("-1 days"));
    $thirty_yesterday = date('Y-m-d',strtotime("-30 days"));
    $before_yesterday = date('Y-m-d',strtotime("-31 days"));
    $thirty_before_yesterday = date('Y-m-d',strtotime("-60 days"));

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://tickets.coworking-metz.fr/api/users-stats?sort=presencesJours&key=' . API_KEY_TICKET . '&from=' . $thirty_yesterday . '&' . $yesterday,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    
    $response_yesterday = curl_exec($curl);
    curl_close($curl);
    
    $json_yesterday = json_decode($response_yesterday, true);

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://tickets.coworking-metz.fr/api/users-stats?sort=presencesJours&key=' . API_KEY_TICKET . '&from=' . $thirty_before_yesterday . '&' . $before_yesterday,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
      ));
    
    $response_before_yesterday = curl_exec($curl);
    curl_close($curl);
      
    $json_before_yesterday = json_decode($response_before_yesterday, true);
    
    $html = '<div class="top-20"><table class="first-top"><tr>';
    
    $rank = 0;
    $last_score = false;
    $rows = 0;

    for($i=0; $i<15; $i++){
        $rows++;
            if( $last_score!= round($json_yesterday[$i]['presencesJours']) ){
                $last_score = round($json_yesterday[$i]['presencesJours']);
                $rank = $rows;
            }

            if ( $json_yesterday[$i]['ranking'] > $json_before_yesterday[$i]['ranking'] ) {
                $up_down = '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/fleche-verte.png">';
            } elseif ( $json_yesterday[$i]['ranking'] < $json_before_yesterday[$i]['ranking'] ) {
                $up_down = '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/fleche-rouge.png">';
            } else {
                $up_down = '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/fleche-orange.png">';
            }

        $rank == 1 ? $html .= '<td class="top-position">1<sup>er</sup></td><td class="name-position">' . $json_yesterday[$i]['firstName'] . '  ' . 
        substr($json_yesterday[$i]['lastName'], 0, 1) . '. (' . round($json_yesterday[$i]['presencesJours']) . ' jours) ' . 
        '</td><td><span class="arrow">' . $up_down . '</span></td></tr>' : $html .= '<td class="top-position">' . $rank . '<sup>ème</sup></td><td class="name-position">' . $json_yesterday[$i]['firstName'] . '  ' . 
        substr($json_yesterday[$i]['lastName'], 0, 1) . '. (' . round($json_yesterday[$i]['presencesJours']) . ' jours) ' . 
        '</td><td><span class="arrow">' . $up_down . '</span></td></tr>' ;

    }

    $html .= '</table><table class="last-top"><tr>';

    for($i=15; $i<30; $i++){
        $rows++;
        if( $last_score!= round($json_yesterday[$i]['presencesJours']) ){
            $last_score = round($json_yesterday[$i]['presencesJours']);
            $rank = $rows;
        }
        if ( $json_yesterday[$i]['ranking'] > $json_before_yesterday[$i]['ranking'] ) {
            $up_down = '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/fleche-verte.png">';
        } elseif ( $json_yesterday[$i]['ranking'] < $json_before_yesterday[$i]['ranking'] ) {
            $up_down = '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/fleche-rouge.png">';
        } else {
            $up_down = '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/fleche-orange.png">';
        }

        $html .= '<td class="top-position">' . $rank . '<sup>ème</sup></td><td class="name-position">' . $json_yesterday[$i]['firstName'] . '  ' . 
        substr($json_yesterday[$i]['lastName'], 0, 1) . '. (' . round($json_yesterday[$i]['presencesJours']) . ' jours) ' . '</td><td><span class="arrow">' . $up_down . '</span></td></tr>' ;
    }

    $html .= '</table></div>';

    echo $html;

}