<?php
// 10 - Jours coworkes année passée


$file = 'http://tickets.coworking-metz.fr/tickets-stats/stats'; 

$data = file_get_contents($file); 

$obj = json_decode($data);

echo round($obj->nb_jours_coworkes_annee_precedente);

?>