<?php
defined('ABSPATH') || exit;

$uploads = content_url('uploads/');
$logo    = $uploads . '2016/04/logo-le-poulailler-seul-carre.png';
$photo   = $uploads . '2020/05/20200524_115733-1140x641.jpg';
$svg_dir      = dirname(__DIR__) . '/templates/assets/';
$svg_url      = content_url('mu-plugins/templates/assets/');
$logo_clair   = $svg_url . 'logo-poulailler-blanc.svg?v=' . (@filemtime($svg_dir . 'logo-poulailler-blanc.svg') ?: 1);
$logo_sombre  = $svg_url . 'logo-poulailler.svg?v=' . (@filemtime($svg_dir . 'logo-poulailler.svg') ?: 1);
$redirect = $_GET['redirect'] ?? false;
$lien_mail = '/wp-login.php?redirect=' . urlencode($redirect) . '&login-link-email=';
$annee = (int) wp_date('Y') - 1;

// Message de redirection du site (?notification=…), affiché sans HTML libre
$notif = null;
if (!empty($_GET['notification'])) {
    $n = json_decode(wp_unslash($_GET['notification']), true);
    if (is_array($n) && (!empty($n['titre']) || !empty($n['texte']))) {
        $notif = [
            'type'  => ($n['type'] ?? '') === 'error' ? 'error' : 'ok',
            'titre' => wp_strip_all_tags((string) ($n['titre'] ?? '')),
            'texte' => wp_kses((string) ($n['texte'] ?? ''), ['a' => ['href' => true], 'br' => []]),
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,follow">
    <title>Connexion - Le Poulailler, Coworking Metz</title>
    <link rel="icon" type="image/png" sizes="32x32" href="https://coworking-metz.fr/favicon/favicon-32x32.png?v=2">
    <link rel="shortcut icon" href="https://coworking-metz.fr/favicon/favicon.ico?v=2">
    <meta name="theme-color" content="#f2af10">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Montserrat:wght@300&display=swap" rel="stylesheet">
    <style>
        :root {
            --y: #eab234;
            --y2: #ffc80b;
            --ink: #1a1e26;
            --ink2: #2b3140;
            --mut: #6b7280;
            --line: #e7e2d6
        }

        * {
            box-sizing: border-box;
            margin: 0
        }

        body {
            font-family: Poppins, system-ui, sans-serif;
            color: var(--ink);
            background: #fff;
            -webkit-font-smoothing: antialiased
        }

        a {
            color: inherit
        }

        .f label {
            display: block;
            font-weight: 600;
            font-size: .85rem;
            margin: 1.1rem 0 .4rem
        }

        .f label i {
            color: #c0392b;
            font-style: normal
        }

        .f input[type=text],
        .f input[type=email],
        .f input[type=password] {
            width: 100%;
            font: inherit;
            font-size: 1rem;
            padding: .85rem 1rem;
            border: 1.5px solid var(--line);
            border-radius: 12px;
            background: #fff;
            color: var(--ink);
            transition: .15s
        }

        .f input:focus {
            outline: none;
            border-color: var(--y);
            box-shadow: 0 0 0 4px #eab23433
        }

        .f input.besoin {
            border-color: #c0392b;
            box-shadow: 0 0 0 4px #c0392b22
        }

        .pw {
            position: relative
        }

        .pw button {
            position: absolute;
            right: .6rem;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: none;
            cursor: pointer;
            font-size: 1.1rem;
            opacity: .55
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            width: 100%;
            font: inherit;
            font-weight: 700;
            font-size: .95rem;
            padding: .95rem 1.2rem;
            border-radius: 12px;
            border: 0;
            cursor: pointer;
            text-decoration: none;
            transition: .15s
        }

        .btn.prim {
            background: var(--y);
            color: var(--ink);
            box-shadow: 0 6px 16px #eab23455
        }

        .btn.prim:hover {
            background: var(--y2);
            transform: translateY(-1px)
        }

        .btn.dark {
            background: var(--ink);
            color: #fff;
            margin-top: 1.2rem
        }

        .btn.dark:hover {
            background: var(--ink2)
        }

        .f .btn.prim {
            margin-top: 1.5rem
        }

        .sep {
            display: flex;
            align-items: center;
            gap: .8rem;
            color: var(--mut);
            font-size: .78rem;
            margin: 1.4rem 0 .2rem
        }

        .sep:before,
        .sep:after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--line)
        }

        .souvenir {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-top: 1rem;
            font-size: .85rem;
            color: var(--mut);
            font-weight: 400
        }

        .f label.souvenir {
            display: flex;
            margin: 1rem 0 0;
            font-weight: 400
        }

        .souvenir input {
            width: 1.05rem;
            height: 1.05rem;
            accent-color: var(--y)
        }

        .lost {
            display: block;
            text-align: center;
            margin-top: 1.2rem;
            font-size: .85rem;
            color: var(--mut)
        }

        .lost:hover {
            color: var(--ink)
        }

        .join {
            margin-top: 1.6rem;
            padding-top: 1.2rem;
            border-top: 1px solid var(--line);
            font-size: .85rem;
            color: var(--mut);
            text-align: center
        }

        .join a {
            color: var(--ink);
            font-weight: 600;
            text-decoration-color: var(--y);
            text-underline-offset: 3px
        }

        h1.t {
            font-size: 1.9rem;
            font-weight: 800;
            letter-spacing: -.02em;
            line-height: 1.15
        }

        .sub {
            color: var(--mut);
            font-size: .92rem;
            margin-top: .5rem
        }

        /* messages (erreurs de connexion, notifications) */
        .msgs {
            margin-top: 1.2rem
        }

        .msgs ul {
            list-style: none;
            padding: 0
        }

        .msgs ul,
        .msgs .msg {
            border-radius: 12px;
            padding: .8rem 1rem;
            font-size: .85rem;
            line-height: 1.45;
            background: #fff6e0;
            border: 1px solid #f1dca0
        }

        .msgs ul.woocommerce-error,
        .msgs .msg.error {
            background: #fdecea;
            border-color: #f3b9b3;
            color: #8a1f17
        }

        .msgs .msg b {
            display: block
        }

        .msgs a {
            font-weight: 600
        }

        .msgs>*+* {
            margin-top: .6rem
        }

        .v1 {
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            min-height: 100vh
        }

        .v1 .side {
            position: relative;
            color: #fff;
            padding: 3rem 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: linear-gradient(180deg, #1a1e26bb 0%, #1a1e26e6 100%), url(<?= esc_url($photo) ?>) center/cover
        }

        .v1 .brand {
            --h: 56px;
            display: flex;
            align-items: center;
            gap: 0;
            text-decoration: none
        }

        .v1 .brand .brand__logo {
            display: block;
            height: var(--h);
            width: auto
        }

        .v1 .brand .brand__since {
            align-self: flex-end;
            margin: 0 0 calc(var(--h) * .104) calc(var(--h) * -.2375 + 1.5em);
            font: 300 calc(var(--h) * .199)/1 'Montserrat', 'Poppins', system-ui, sans-serif;
            letter-spacing: .02em;
            text-transform: uppercase;
            color: #fff;
            white-space: nowrap
        }

        .v1 .pitch h2 {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: -.02em;
            max-width: 18ch
        }

        .v1 .pitch h2 em {
            font-style: normal;
            color: var(--y)
        }

        .v1 .pitch p {
            margin-top: 1rem;
            max-width: 42ch;
            opacity: .85;
            line-height: 1.6;
            font-size: .95rem
        }

        .v1 .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 2rem
        }

        .v1 .stat {
            background: #ffffff14;
            border: 1px solid #ffffff22;
            border-radius: 16px;
            padding: 1rem 1.1rem;
            backdrop-filter: blur(6px)
        }

        .v1 .stat b {
            display: block;
            font-size: 1.9rem;
            font-weight: 800;
            color: var(--y)
        }

        .v1 .stat span {
            font-size: .75rem;
            opacity: .8;
            line-height: 1.3;
            display: block
        }

        .v1 .tags {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            font-size: .78rem
        }

        .v1 .tags span {
            background: #ffffff1a;
            border-radius: 999px;
            padding: .35rem .8rem
        }

        .v1 .main {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            background: #fff
        }

        .v1 .box {
            width: 100%;
            max-width: 410px
        }

        @media(max-width:900px) {
            .v1 {
                display: flex;
                flex-direction: column;
                min-height: 100vh
            }

            .v1 .side {
                display: contents
            }

            .v1 .brand {
                order: 1;
                background: #fff;
                color: var(--ink);
                padding: 1.1rem 1.5rem;
                border-bottom: 1px solid var(--line)
            }

            .v1 .brand {
                --h: 44px
            }

            .v1 .brand .brand__since {
                color: #000
            }

            .v1 .main {
                order: 2;
                padding: 2rem 1.5rem 2.5rem
            }

            .v1 .pitch {
                order: 3;
                color: #fff;
                padding: 2.5rem 1.5rem 2rem;
                background: linear-gradient(180deg, #1a1e26cc, #1a1e26f2), url(<?= esc_url($photo) ?>) center/cover
            }

            .v1 .pitch h2 {
                font-size: 1.7rem
            }

            .v1 .stats {
                gap: .6rem
            }

            .v1 .stat {
                padding: .8rem .7rem
            }

            .v1 .stat b {
                font-size: 1.4rem
            }

            .v1 .stat span {
                font-size: .68rem
            }

            .v1 .tags {
                order: 4;
                background: var(--ink);
                color: #fff;
                padding: 0 1.5rem 2.5rem
            }

            .v1 h1.t {
                font-size: 1.6rem
            }
        }
    </style>
    <?php
    // Bulle de chat du site (pré-collecte + widget Brevo), enregistrée par plugins/brevo.php
    do_action('wp_enqueue_scripts');
    wp_print_styles(['brevo-precollect']);
    ?>
</head>

<body>
    <div class="v1">
        <section class="side">
            <a class="brand" href="<?= esc_url(home_url('/')) ?>" aria-label="Le Poulailler, Coworking Metz — accueil">
                <picture>
                    <source media="(max-width:900px)" srcset="<?= esc_url($logo_sombre) ?>">
                    <img class="brand__logo" src="<?= esc_url($logo_clair) ?>" alt="Le Poulailler — Coworking Metz" width="150" height="44">
                </picture>
                <span class="brand__since">depuis 2014</span>
            </a>
            <div class="pitch">
                <h2>Un bureau qui donne <em>envie</em> d'aller au bureau.</h2>
                <p>Coworking associatif de <?= (int) COWORKING_NB_PLACES ?> places basé à Metz, au sein du tiers-lieu BLIIIDA.</p>
                <div class="stats">
                    <div class="stat"><b data-k="nb_coworkers_veille">–</b><span>coworkers présents hier</span></div>
                    <div class="stat"><b data-k="nb_coworkers_debut">–</b><span>coworkers accueillis depuis 2014</span></div>
                    <div class="stat"><b data-k="nb_jours_presence_annee_precedente">–</b><span>journées travaillées en <?= $annee ?></span></div>
                </div>
            </div>
            <div class="tags"><span>☕ Petit-déj mensuel</span><span>🍽 Cantine sur place</span><span>🌿 Terrasse &amp; jardin</span><span>🎮 Baby-foot &amp; consoles</span><span>🤝 Association de bénévoles</span></div>
        </section>
        <section class="main">
            <div class="box">
                <h1 class="t">Bienvenue au Poulailler</h1>
                <p class="sub">Connectez-vous pour accéder à votre espace coworker.</p>

                <div class="msgs">
                    <?php if ($notif) { ?>
                        <div class="msg <?= $notif['type'] ?>"><?php if ($notif['titre']) { ?><b><?= esc_html($notif['titre']) ?></b><?php } ?><?= $notif['texte'] ?></div>
                    <?php } ?>
                    <?php if (function_exists('wc_print_notices')) wc_print_notices(); ?>
                </div>

                <form class="f woocommerce-form woocommerce-form-login login" method="post" novalidate>
                    <a class="btn prim login-link" id="lien-mail" href="<?= esc_url($lien_mail) ?>">🔓 Recevoir un lien de connexion par mail</a>
                    <div class="sep">ou avec votre mot de passe</div>

                    <label for="username">Email <i>*</i></label>
                    <input type="text" name="username" id="username" autocomplete="username" placeholder="vous@exemple.fr" value="<?= (!empty($_POST['username']) && is_string($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : '' ?>" required aria-required="true">

                    <label for="password">Mot de passe <i>*</i></label>
                    <div class="pw"><input type="password" name="password" id="password" autocomplete="current-password" placeholder="••••••••" required aria-required="true"><button type="button" id="voir-mdp" aria-label="Afficher le mot de passe">👁</button></div>

                    <?php do_action('woocommerce_login_form'); ?>

                    <label class="souvenir"><input name="rememberme" type="checkbox" id="rememberme" value="forever"> Rester connecté</label>

                    <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
                    <button class="btn dark woocommerce-form-login__submit" type="submit" name="login" value="<?= esc_attr__('Log in', 'woocommerce') ?>">Connexion via mot de passe</button>
                    <a class="lost" href="<?= esc_url(wp_lostpassword_url()) ?>">Mot de passe perdu ?</a>

                    <?php do_action('woocommerce_login_form_end'); ?>

                    <p class="join">Pas encore coworker ? <a href="<?= esc_url(home_url('/nous-rejoindre/')) ?>">Réservez votre journée d'essai gratuite</a></p>
                </form>
            </div>
        </section>
    </div>

    <script>
        (function() {
            var u = document.getElementById('username'),
                p = document.getElementById('password');
            document.getElementById('voir-mdp').addEventListener('click', function() {
                p.type = p.type === 'password' ? 'text' : 'password';
            });
            // Lien de connexion par mail : l'adresse saisie est ajoutée au lien
            var l = document.getElementById('lien-mail');
            l.addEventListener('click', function(e) {
                e.preventDefault();
                var v = u.value.trim();
                if (v.indexOf('@') < 1) {
                    u.classList.add('besoin');
                    u.focus();
                    u.placeholder = 'Saisissez d\'abord votre email';
                    return;
                }
                window.location.href = l.getAttribute('href') + encodeURIComponent(v);
            });
            u.addEventListener('input', function() {
                u.classList.remove('besoin');
            });
            // Statistiques en direct
            fetch('/api-json-wp/cowo/v1/stats').then(function(r) {
                return r.json();
            }).then(function(d) {
                document.querySelectorAll('[data-k]').forEach(function(el) {
                    var n = Math.round(d[el.dataset.k] || 0),
                        t = 0,
                        s = Math.max(1, Math.round(n / 40));
                    var iv = setInterval(function() {
                        t = Math.min(n, t + s);
                        el.textContent = t.toLocaleString('fr-FR');
                        if (t >= n) clearInterval(iv)
                    }, 25);
                });
            }).catch(function() {});
        })();
    </script>
    <?php wp_print_scripts(['brevo-script']); ?>
</body>

</html>
