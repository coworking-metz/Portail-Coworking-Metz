jQuery(function ($) {
    console.log($);
    /*var p = $('.tickets-status p', '.woocommerce-account.logged-in');
    p.each(function ( index ) {
        //date abonnement
        if ($(this).html().slice(0, 13) == 'Tu disposes d') {
            //Tu disposes d'un abonnement qui prendra fin le <em>2019-02-01</em>
            var subscriptionDate = $(this).find('em');
            var d = new Date(subscriptionDate.text() );
            var dateFormated = d.toLocaleDateString('fr-FR', { weekday: "long", year: 'numeric', month: 'long', day: 'numeric' });

            $(this).html('Tu disposes d’un abonnement valable jusqu’au <em>' + dateFormated + '</em>.');
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
    });*/
});