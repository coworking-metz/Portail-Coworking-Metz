<?php
define('WP_CACHE', false); // Added by WP Rocket

// Added by WP Rocket
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clefs secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C'est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d'installation. Vous n'avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */
//ini_set('log_errors',TRUE);
//ini_set('error_reporting', E_ALL);
//ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
//define('FS_METHOD', 'direct');
define('WP_POST_REVISIONS', 10);
define('AUTOSAVE_INTERVAL', 180); // Seconds
define('DISALLOW_FILE_EDIT', true);
define('API_KEY_TICKET', 'bupNanriCit1');
// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'coworking');
/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'root');
/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', '');
/** Adresse de l'hébergement MySQL. */
define('DB_HOST', 'localhost');
/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8mb4');
define('WP_MEMORY_LIMIT', '1024M');
define('WP_MAX_MEMORY_LIMIT', '512M');
/** Type de collation de la base de données.
 * N'y touchez que si vous savez ce que vous faites.
 */
define('DB_COLLATE', '');
/**#@+
 * Clefs uniques d'authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '0yjx{u+gyh&!,f9r=A9b4GZ t=5J6kjkFF^+:80L]1M 3MW-q>&bVTlm24#w7J,P');
define('SECURE_AUTH_KEY',  '3(i&2bM~<#}!^GO<{#H#f#hu-l_VBHZwKa?8.::R W_+`ayBuBhbS+$nU=Dk)E+4');
define('LOGGED_IN_KEY',    'rP>u_Ltw1@gFDhV4h1o/OguX#im+In@;CLnY;Xc!-*BU~O%#O)(K3M/7HF,:(5Rh');
define('NONCE_KEY',        'Kqo5)0(LN4m/5qDRgA@}k:>x8_ls:mRVV}|Hp)z6769iv)lSb0XmtSrq{FF+#=7V');
define('AUTH_SALT',        '%>w~nARw&aJ>m^)[SJ :2aNcJTlAO|`ymB}r9gFyy@3j5rvtc?;TrUxvdq,2*h2R');
define('SECURE_AUTH_SALT', 'alpnyrB{RFzNAVype|JVJ|.Z-urO@h@rL&-+?LvOqGX_CQ23`p-+vA&<)^%RR)fM');
define('LOGGED_IN_SALT',   '6~: v{oYkA5DIYon#z,ra3-0|^_P,/;cYPA5`y+t%*SHZE}w+-Q$&7VBroGVnFIR');
define('NONCE_SALT',       'K7H0a*I%Ik_Q-TNqucAlJQ}+YO?& x=Qb?4{i.3_2vg&fG,0iNu.8zizsHa#DR:Z');
define('JWT_AUTH_SECRET_KEY', 'cp!Xbu]~~neV^i_>[E}VCiq~l T<7G:7q-|8mZ{,8m.6p`{9rTod5oJf?KfI60rv');
/**#@-*/
/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix  = 'prfxcrwkng_';
/**
 * Pour les développeurs : le mode deboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l'affichage des
 * notifications d'erreurs pendant votre essais.
 * Il est fortemment recommandé que les développeurs d'extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 */
if (isset($_GET['errors'])) {
  error_reporting(-1);
  ini_set('display_errors', 'On');
  define('WP_DEBUG', true);
} else {
  define('WP_DEBUG', false);
  define('WP_DEBUG_DISPLAY', false);
}

#ssl config from https://codex.wordpress.org/Administration_Over_SSL
//define('FORCE_SSL_ADMIN', true);
// in some setups HTTP_X_FORWARDED_PROTO might contain
// a comma-separated list e.g. http,https
// so check for https existence
//if (strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false)
// $_SERVER['HTTPS']='on';
/* C'est tout, ne touchez pas à ce qui suit ! Bon blogging ! */
/** Chemin absolu vers le dossier de WordPress. */
if (!defined('ABSPATH'))
  define('ABSPATH', dirname(__FILE__) . '/');
/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');
//define('FS_METHOD','direct');