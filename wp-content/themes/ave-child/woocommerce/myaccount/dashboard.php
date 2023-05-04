<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="my-account-user-balance">
<?php 
    //the_coworking_user_balance();
?>
</div>
<div class="my-account-user-balance-api">
<?php 
    api_user_balance();
?>
</div>

<!-- <script>
	jQuery(function ($) {
    var p = $('.tickets-status p', '.woocommerce-account.logged-in');
    p.each(function ( index ) {
        //Il te reste 
        if ($(this).html().slice(0, 11) == 'Il te reste') {
            var str = $(this).html();
            $(this).html( str.substring(0, str.length-8) + 'entre 23h00 et 00h00. Le décompte des tickets se fait de la manière suivante : <br>- entre 0 et 5h de présence sur une journée : 1/2 ticket ;<br>- plus de 5h de présence sur une journée : 1 ticket.' );
        }
        //date abonnement
        if ($(this).html().slice(0, 13) == 'Tu disposes d') {
            //Tu disposes d'un abonnement qui prendra fin le <em>2019-02-01</em>
            var subscriptionDate = $(this).find('em');
            var d = new Date(subscriptionDate.text() );
            var dateFormated = d.toLocaleDateString('fr-FR', { weekday: "long", year: 'numeric', month: 'long', day: 'numeric' });

            $(this).html('Tu disposes d’un abonnement valable jusqu’au <em>' + dateFormated + '</em> inclus.');
        }

        //carte de membre
        if ($(this).html().slice(0, 18) == 'Ta carte de membre') {
            var memberCardDate = $(this).find('em');
            if(memberCardDate) {
                //current Year
                var currentYear = new Date().getFullYear();
     
                //membershipDate Year
                var dYear = Number( memberCardDate.text().slice(6) ) - 1;
     
                if(dYear == currentYear) {
                    $(this).html('Tu disposes d’une carte d’adhérent à jour pour l’année <em>' + dYear +'</em>.');
                } else {
                    $(this).html('Ta carte d’adhérent était valable jusqu’en <em>' + dYear + '</em>.<br>La carte d’adhérent est <a title="commander ma carte d’adhérent" href="https://www.coworking-metz.fr/boutique/carte-adherent/" style="text-decoration: underline; text-transform: uppercase; font-weight: bold;">obligatoire</a>, merci de la renouveller dès que possible pour <em>' + currentYear +'</em>.');
                }
            }
        }
    });
});
</script> -->

<div class="reglement"></div>

<br>


<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
