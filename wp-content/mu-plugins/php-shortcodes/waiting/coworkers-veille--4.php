<?php
// 4 - Coworkers veille


$file = 'http://tickets.coworking-metz.fr/tickets-stats/stats'; 

$data = file_get_contents($file); 

$obj = json_decode($data);

echo $obj->nb_coworkers_veille;

?>