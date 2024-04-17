<?php
// 3 - coworkersNow


    global $user_email;
      get_currentuserinfo();

    $data = array("key" => "bupNanriCit1", "email" => $user_email);
    $data_string = json_encode($data);

    $ch = curl_init('https://tickets.coworking-metz.fr/coworkersNow');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );

    echo curl_exec($ch);

 ?>