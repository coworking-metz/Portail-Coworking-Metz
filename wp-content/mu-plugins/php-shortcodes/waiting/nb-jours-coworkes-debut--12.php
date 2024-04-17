<?php
// 12 - Nb jours coworkés début


$file = 'http://tickets.coworking-metz.fr/tickets-stats/stats'; 

$data = file_get_contents($file); 

$obj = json_decode($data);

echo round($obj->nb_jours_coworkes_debut);

?>