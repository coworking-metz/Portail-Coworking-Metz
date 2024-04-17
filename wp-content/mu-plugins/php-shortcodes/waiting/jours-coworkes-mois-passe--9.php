<?php
// 9 - Jours coworkes mois passé


$file = 'http://tickets.coworking-metz.fr/tickets-stats/stats'; 

$data = file_get_contents($file); 

$obj = json_decode($data);

echo round($obj->nb_jours_coworkes_mois_precedent);

?>