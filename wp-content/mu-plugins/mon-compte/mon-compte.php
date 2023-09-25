<?php




add_action('init', function () {
    if (is_admin()) return;

    $t = filemtime(ABSPATH . '/wp-content/mu-plugins/mon-compte/mon-compte.css');
    wp_enqueue_style('mon-compte', '/wp-content/mu-plugins/mon-compte/mon-compte.css', array(), $t, false);
    $t = filemtime(ABSPATH . '/wp-content/mu-plugins/mon-compte/mon-compte.js');
    wp_enqueue_script('mon-compte', '/wp-content/mu-plugins/mon-compte/mon-compte.js', array(), $t, false);
});

function mon_compte_acces_portail_parking()
{
?>

    <h3>Accès piéton / vélos</h3>
    <p>L'accès au coworking se fait via l'entrée piétonne de Bliiida situé au 7 avenue de Blida (<a href="https://www.google.com/maps/@49.1265285,6.1827006,3a,21.5y,93.61h,88.17t/data=!3m6!1e1!3m4!1se4cSiTcrlxFLNi_S8iZC8A!2e0!7i16384!8i8192?entry=ttu" target="_blank"><u>voir photo</u></a>)</p>
    <p>Cette entrée est réservée aux porteurs d'un badge d'accès OU <strong>aux coworkers actifs, qui peuvent déverrouiller le portail en utilisant <a href="/application-compagnon-coworking/"><u>l'application Compagnon Coworking</u></a></strong>.</p>

    <h3>Accès voitures / motos / etc.</h3>
    <p>En tant que membre de l'association Coworking, vous pouvez garer votre véhicule pendant vos journées de coworking dans le parking de Bliiida.
    <p>L'accès se fait au bout de l'avenue de Blida, en face de l'usine UEM (<a href="https://maps.app.goo.gl/Hoaoh3oWFqDHn2mv7" target="_blank"><u>Voir le plan</u></a>).</p>
    <p>Le portail du parking peut-être déverouillé via un bip sans fil OU <strong>en utilisant <a href="/application-compagnon-coworking/"><u>l'application Compagnon Coworking</u></a> réservée aux coworkers actifs</strong>.</p>
    <p>Cette fonctionnalité actuelle en beta test est pour l'instant gratuite est accessible à tous. Une fois la phase de beta test terminé, il s'agira d'un option payante.</p>

<?php
}
