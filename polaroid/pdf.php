<?php
require 'vendor/autoload.php';
define('WP_USE_THEMES', false); // We don't want to use themes.
require('../wp-load.php');


$id = $_GET['id']??false;

$w=100;
$h=148;

if(!$id) exit;

$image_url = site_url().'/polaroid/'.$id.'-hd.jpg';

// Télécharger l'image dans un fichier temporaire
$image_path = tempnam(sys_get_temp_dir(), 'pdf_image').'.jpg';
file_put_contents($image_path, file_get_contents($image_url));

// Créer une instance PDF
$pdf = new FPDF('P', 'mm', array($w, $h));

// Ajouter une page
$pdf->AddPage();

// Insérer l'image
$pdf->Image($image_path, 0, 0, 100, 122.3);

// Supprimer le fichier temporaire
unlink($image_path);

// Sauvegarder le PDF
$pdf->Output('I',$id.'.pdf');