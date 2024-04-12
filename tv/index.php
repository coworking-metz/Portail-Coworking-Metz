<?php



$files = glob(__DIR__.'/*.*');
$ecrans= ['https://ecrans.coworking-metz.fr/visionner/entree', 'https://ecrans.coworking-metz.fr/visionner/espace-detente'];
echo '<ol>';
foreach($ecrans as $ecran) {
    $file = basename($ecran);
    echo '<li><a href="'.$ecran.'">'.$file.'</a>';
}
echo '</ol>';

echo '<ol>';
foreach($files as $file) {
    if(strstr($file, 'php')) continue;
    $file = basename($file);
    echo '<li><a href="'.$file.'">'.$file.'</a>';
}
echo '</ol>';
