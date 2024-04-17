<?php
// 7 - Coworkers année passée


$file = 'http://tickets.coworking-metz.fr/tickets-stats/stats'; 

$data = file_get_contents($file); 

$obj = json_decode($data);

echo $obj->nb_coworkers_annee_precedente;

?>