<?php
function isImagePhoto($url)
{
    $payload = askGPT('Dis moi si ce fichier image est bien une photo en prise de vue réelle et quelle contient bien un visage humain. Ta réponse sera composée d\'un mot : true si c\'est une photo avec un visage, false sinon', ['image' => $url]);

    if($payload['status']!= 'success') return true; // dans le cas où chatgpt n'a pas sur donner une réponse claire
    if($payload['response'] === 'true') return true;
}

function isImagePhotoConnue($url)
{
    $payload = askGPT('Dis moi si cette photo présente le visage d\'une personnalité connue. Ta réponse sera composée du nom de la personnalité connue, "false" sinon', ['image' => $url]);

    if($payload['status']!= 'success') return true; // dans le cas où chatgpt n'a pas sur donner une réponse claire
    if($payload['response'] === 'true') return true;
}

function generer_image_alpha($url)
{

    $alpha = get_post_by_meta('original', $url, 'attachment');

    if ($alpha) {
        return wp_get_attachment_url($alpha->ID);
    } else {
        $api = 'https://tools.sopress.net/remove-background/?raw&force=true&crop=false&image=' . urlencode($url);
        $alpha_url = file_get_contents($api);
        $alpha_id = insert_attachment_from_file($alpha_url, [], ['original' => $url], 1000);
        return wp_get_attachment_url($alpha_id);
    }
}
/**
 * Converts a PNG image to a JPEG image
 *
 * @param string $from The path to the PNG image to convert
 * @param string|bool $to The path to save the JPEG image to
 * @param int $quality The quality of the JPEG image
 *
 * @return string|bool The path to the JPEG image if successful, otherwise false
 */
function pngToJpeg($from, $to = false, $quality = 80)
{

    if (!$to) {
        $to = str_replace('.png', '.jpg', $from);
    }
    $image = imagecreatefromfile($from);
    if (!$image) return false;

    $ret = imagejpeg($image, $to, $quality);
    imagedestroy($image);

    return $ret ? $to : false;
}
