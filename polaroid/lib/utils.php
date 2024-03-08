<?php



/**
 * Outputs an image with the correct headers.
 * If the image is a PNG, it converts it to JPG before outputting.
 * Resizes the image to the given width while keeping the aspect ratio if width is set.
 * 
 * @param string $imagePath Path to the image file.
 * @param int|null $width Optional width to resize the image.
 * @param string $destinationPath Path where the  image will be written.
 */
function outputImageWithHeaders($imagePath, $width = null, $destinationPath = false)
{
    if (!file_exists($imagePath)) {
        header('HTTP/1.0 404 Not Found');
        echo 'File not found.';
        return;
    }

    $fileInfo = pathinfo($imagePath);
    $extension = strtolower($fileInfo['extension']);

    if ($extension === 'png') {
        $image = imagecreatefrompng($imagePath);
    } else {
        $image = imagecreatefromstring(file_get_contents($imagePath));
    }

    if ($width && imagesx($image) > $width) {
        $height = (int) (imagesy($image) * ($width / imagesx($image)));
        $resizedImage = imagescale($image, $width, $height);
        imagedestroy($image);
        $image = $resizedImage;
    }
    if ($extension === 'png') {
        header('Content-Type: image/jpeg');
        write_and_output_image($image, $destinationPath, 'jpg');
    } else {
        $mimeType = mime_content_type($imagePath);
        header("Content-Type: $mimeType");
        if ($mimeType === 'image/jpeg') {
            write_and_output_image($image, $destinationPath, 'jpg');
        } elseif ($mimeType === 'image/gif') {
            write_and_output_image($image, $destinationPath, 'gif');
        } elseif ($mimeType === 'image/png') {
            write_and_output_image($image, $destinationPath, 'png');
        }
    }

    imagedestroy($image);
    exit;
}


function write_and_output_image($image, $path, $type)
{
    if ($type == 'jpg') {
        imagejpeg($image);
        return imagejpeg($image, $path);
    }
    if ($type == 'gif') {
        imagegif($image);
        return imagegif($image, $path);
    }
    if ($type == 'png') {
        imagepng($image);
        return imagepng($image, $path);
    }
}

/**
 * Affiche le contenu d'une image si elle existe, est plus récente que l'âge spécifié et termine l'exécution du script.
 * 
 * @param string $imagePath Chemin vers l'image.
 * @param int $age Âge maximum de l'image en secondes. Par défaut, un mois.
 * @return mixed
 */
function outputImageIfExists($imagePath, $age = 2592000)
{

    if (file_exists($imagePath)) {
        $fileAge = time() - filemtime($imagePath);
        if ($fileAge > $age) {
            return unlink($imagePath);
        }
        $mime_type = mime_content_type($imagePath);
        CF::cacheHeaders($age);
        header('Content-Type: '.$mime_type);

        readfile($imagePath);
        exit();
    }
}
