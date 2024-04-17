<?php
// 2 - balance


    global $user_email;
      get_currentuserinfo();

    $data = array("key" => "bupNanriCit1", "email" => $user_email);
    $data_string = json_encode($data);

    $ch = curl_init('http://tickets.coworking-metz.fr/balance');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );

    //$result = json_decode(curl_exec($ch));

    //if ($result && $result['status'] == 200) echo $result['message'];
    //else echo 'Erreur lors de la récupération des informations.';

    echo curl_exec($ch);

 ?>