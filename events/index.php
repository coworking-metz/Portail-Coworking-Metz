<?php
include __DIR__ . '/inc/main.inc.php';

if ($id = $_POST['id'] ?? false) {
    $email = $_POST['email'] ?? false;
    $participe = $_POST['participe'] ?? false;
    $participation = upsertParticipation($id, ['email' => $email, 'participe' => $participe, 'id_evenement' => $id]);
    rediriger(urlEvenement($id).'?email='.urlencode($email));
}

$id = $_GET['id'] ?? false;
$email = $_GET['email'] ?? '';

$evenement = getEvenement($id);

$participation = getParticipation($email, $id);

$participe = $participation['participe'] ?? false;


$titre = htmlspecialchars($evenement['evenement'] . ' - Participation');
$description = descriptionEvenement($evenement);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $titre ?></title>
    <meta name="description" content="Evenement <?= $description ?>" />
    <base href="/events/">


    <link rel="apple-touch-icon" sizes="180x180" href="https://coworking-metz.fr/favicon/apple-touch-icon.png?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="https://coworking-metz.fr/favicon/favicon-32x32.png?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="https://coworking-metz.fr/favicon/favicon-16x16.png?v=2">
    <link rel="manifest" href="https://coworking-metz.fr/favicon/site.webmanifest?v=2">
    <link rel="mask-icon" href="https://coworking-metz.fr/favicon/safari-pinned-tab.svg?v=2" color="#f3af10">
    <link rel="shortcut icon" href="https://coworking-metz.fr/favicon/favicon.ico?v=2">
    <meta name="apple-mobile-web-app-title" content="<?= $titre; ?>">
    <meta name="application-name" content="<?= $titre; ?>">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="https://coworking-metz.fr/favicon/browserconfig.xml?v=2">
    <meta name="theme-color" content="#000000">




    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="description" content="<?= $description; ?>">
    <meta name="msapplication-config" content="https://coworking-metz.fr/favicon/browserconfig.xml">
    <meta name="msapplication-TileColor" content="#f2af10">
    <meta name="theme-color" content="#f2af10">
    <meta name="title" content="<?= $titre; ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:description" content="<?= $description; ?>">
    <meta name="twitter:image" content="https://coworking-metz.fr/favicon/social.jpg">

    <meta property="og:description" content="<?= $description; ?>">
    <meta property="og:image" content="https://coworking-metz.fr/favicon/social.jpg">
    <meta property="og:image:alt" content="<?= $titre; ?>">
    <meta property="og:image:secure_url" content="https://coworking-metz.fr/favicon/social.jpg">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:url" content="https://coworking-metz.fr/favicon/social.jpg">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:site_name" content="<?= $titre; ?>">
    <meta property="og:title" content="<?= $titre; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://rejoindre.coworking-metz.fr/">

    <meta name="description" content="Comment devenir coworker ?" />
    <link rel="shortcut icon" href="https://www.coworking-metz.fr/favicon.ico" />
    <!-- Pico.css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css" />

    <!-- Custom styles for this example -->
    <link rel="stylesheet" href="css/style.css" />
</head>

<body>

    <!-- Main -->
    <main class="container">
        <article class="grid">
            <div>
                <hgroup>
                    <h1><?= htmlspecialchars($evenement['evenement']); ?></h1>
                    <p>Le <?= formatDateToFrench($evenement['date']); ?>
                    <?php if ($evenement['heure']) { ?>à <?= formatTimeToHHMM($evenement['heure']); ?><?php } ?>
                        <?php if ($evenement['lieu']) { ?><br>Lieu: <?= htmlspecialchars($evenement['lieu']); ?><?php } ?>

                        <br><small><b><?= participationEvenement($evenement) ?></b></small>
                    </p>
                    <h2>Votre réponse: <b><?= texteParticipation($participation); ?></b></h2>
                </hgroup>
                <form method="post">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id); ?>">
                    <label>
                        <?php if ($participation) { ?>
                            <b>Modifiez votre choix</b>
                        <?php } else { ?>
                            <b>Vous participez ?</b>
                        <?php } ?>
                        <input type="email" name="email" placeholder="Votre email" required value="<?= htmlspecialchars($email) ?>" />
                    </label>
                    <?php if ($participe != 'ok') { ?>
                        <button type="submit" name="participe" value="ok">Je participe</button>
                    <?php } ?>
                    <?php if ($participe != 'maybe') { ?>
                        <button type="submit" name="participe" value="maybe" class="contrast">je vais peut-être participer</button>
                    <?php } ?>
                    <?php if ($participe != 'ko') { ?>
                        <button type="submit" name="participe" value="ko" class="outline contrast">Je ne participe pas</button>
                    <?php } ?>
                </form>
            </div>
            <div>
                <div class="duotone"><img src="<?= $evenement['image_url']; ?>)"></div>
                <img src="./img/logo.png">
            </div>
        </article>
    </main>
    <!-- ./ Main -->


</body>

</html>