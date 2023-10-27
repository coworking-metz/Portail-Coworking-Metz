<?php
$target_dir = __DIR__ . "/tmp/";
$target_file_path = $target_dir . 'custom-pola';
$polaroid = false;
$text1 = $_POST['text1'] ?? '';
$text2 = $_POST['text2'] ?? '';
$text3 = $_POST['text3'] ?? '';

// Vérification de la méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Création du dossier s'il n'existe pas
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    // Vérification de l'existence du fichier
    if ($_FILES["image"]["tmp_name"] != '') {
        $file_tmp_path = $_FILES["image"]["tmp_name"];
        $mime_type = mime_content_type($file_tmp_path);

        // Vérification du type d'image
        if (in_array($mime_type, ['image/png', 'image/jpeg', 'image/gif'])) {

            // Génération d'un hash unique basé sur le contenu du fichier
            $content = file_get_contents($file_tmp_path);




            // Chemin complet du fichier

            // Déplacement du fichier
            if (move_uploaded_file($file_tmp_path, $target_file_path)) {
            }
        }
    }
    $polaroid = ['photo' => './tmp/custom-pola', 'nom' => $text1, 'description' => $text2, 'complement' => $text3];
}

$data = ['custom' => true, 'polaroid' => $polaroid];
?>

<!DOCTYPE html>
<html>

<head>
    <title>File Upload Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
</head>

<body>

    <section class="section">
        <div class="container">
            <h1 class="title">Générateur de polaroids</h1>
            <!-- Formulaire d'envoi de fichier -->
            <form action="" method="post" enctype="multipart/form-data">
                <div class="file has-name">
                    <label class="file-label">
                        <input class="file-input" type="file" name="image" id="image">
                        <span class="file-cta">
                            <span class="file-icon">
                                <i class="fas fa-upload"></i>
                            </span>
                            <span class="file-label">
                                Envoyer une photo
                            </span>
                        </span>
                        <span class="file-name">
                            Pas de fichier
                        </span>
                    </label>
                </div>
                <div class="field">
                    <label class="label">Text 1</label>
                    <div class="control">
                        <input class="input" type="text" value="<?= htmlspecialchars($text1); ?>" name="text1">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Text 2</label>
                    <div class="control">
                        <input class="input" type="text" value="<?= htmlspecialchars($text2); ?>" name="text2">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Text 3</label>
                    <div class="control">
                        <input class="input" type="text" value="<?= htmlspecialchars($text3); ?>" name="text3">
                    </div>
                </div>
                <input class="button is-primary" type="submit" value="Valider" name="submit">
            </form>

            <?php
            // Vérification de la méthode POST
            if ($data['polaroid']) { 
                $url = 'https://www.coworking-metz.fr/polaroid/?'.http_build_query($data); 
                ?>
                <br>
                <a target="_blank" href="pdf.php?image_url=<?=urlencode($url);?>"><img width="500" src="<?=$url;?>">
            <?php } ?>
        </div>
    </section>

</body>

</html>