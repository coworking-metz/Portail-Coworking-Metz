<?php
// 18 - Trombine unique
 function picture_user_presence2() {
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://tickets.coworking-metz.fr/api/current-users?key=bupNanriCit1&delay=10',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    
    $user_presence = curl_exec($curl);
    
    curl_close($curl);
    
    
    $json_user_presence = json_decode($user_presence, true);
    
    //DIV wrapper
    echo '<div class="grid-images">';
    
    //Images trombi
    foreach ($json_user_presence as $key => $value){
      $i = 0;
  
      $url_image = get_user_meta($value['wpUserId'], $key = 'image_trombinoscope_front', $single = true );
      $image_array = wp_get_attachment_image_src($url_image);
      $image_url = $image_array[0];
      $user_balance = $value['balance'];
     
        if ($user_balance < 1) {
            echo '<img class="animated-image balance-tickets" style="transform: rotate(' . rand(-6,6) . 'deg); animation-delay: '. (rand(0,1000) + ($i*100)) . 'ms;" src="' . $image_url . '">';
        } else {
            echo '<img class="animated-image" style="transform: rotate(' . rand(-6,6) . 'deg); animation-delay: '. (rand(0,1000) + ($i*100)) . 'ms;" src="' . $image_url . '">';
        }
      $i++;
    }
    //DIV closing
    echo '</div>';
    }

picture_user_presence2();
?>
