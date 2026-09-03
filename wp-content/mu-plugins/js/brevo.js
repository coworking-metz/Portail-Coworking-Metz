document.addEventListener('DOMContentLoaded', () => {
    if (document.location.href.includes('wp-admin')) return;

	if (document.location.hash === '#ouvrir-brevo') {
		window.location.href = window.location.pathname + '#brevoConversationsExpanded';
	}

    // Tant que le visiteur anonyme ne s'est pas nommé, le widget Brevo n'est pas
    // chargé : c'est la bulle de pré-collecte qui prend sa place.
    let brevoCharge = false;
    let ouvrirPrecollecte = null;

    // document.querySelectorAll('[href="#ouvrir-brevo"]').forEach(bouton => bouton.addEventListener('click', e => {
    //     e.preventDefault()
    //     BrevoConversations('openChat', true);
    // }));
    document.addEventListener('click', e => {
        const target = e.target.closest('[href="#ouvrir-brevo"]')
        if (!target) return;
        e.preventDefault()
        if (window.Notifications) Notifications.closeAll()
        if (brevoCharge) {
            BrevoConversations('openChat', true);
        } else if (ouvrirPrecollecte) {
            // on demande d'abord son prénom au visiteur
            ouvrirPrecollecte();
        }
    });

    /**
     * Charge le widget Brevo et lui transmet l'identité du visiteur.
     *
     * @param {object}  user_data     hash + éventuellement email / firstName / lastName…
     * @param {boolean} ouvrirLeChat  ouvrir la fenêtre de discussion une fois prête
     */
    function chargerBrevo(user_data, ouvrirLeChat) {
        if (brevoCharge) {
            // déjà en place : on se contente de mettre l'identité à jour
            BrevoConversations('updateIntegrationData', user_data);
            if (ouvrirLeChat) BrevoConversations('openChat', true);
            return;
        }
        brevoCharge = true;

        window.BrevoConversationsSetup = {
            visitorId: user_data.hash
        };
        (function (d, w, c) {
            w.BrevoConversationsID = '65324d6bf96d92531b4091f8';
            w[c] = w[c] || function () {
                (w[c].q = w[c].q || []).push(arguments);
            };
            var s = d.createElement('script');
            s.async = true;
            s.src = 'https://conversations-widget.brevo.com/brevo-conversations.js';
            s.addEventListener('load', () => {
                if (user_data) {
                    BrevoConversations('updateIntegrationData', user_data);
                }
                if (ouvrirLeChat || document.location.hash.includes('ouvrir-brevo')) {
                    BrevoConversations('openChat', true);
                }
            })
            if (d.head) d.head.appendChild(s);
        })(document, window, 'BrevoConversations');
    }

    fetch('/mon-compte/?is-connected').then(response => response.json()).then(data => {
        const user_data = {}
        if (!data.user) return;

        user_data.hash = data.user.hash;

        if (data.user.ID) {
            // Visiteur connecté : Brevo classique, identité issue du compte WordPress.
            user_data.email = data.user.user_email;
            user_data.firstName = data.user.firstName;
            user_data.lastName = data.user.lastName;
            user_data.phone = null;
            user_data.notes = '';
            // user_data.display_name = data.user.display_name;
            try {
                user_data.roles = data.user.roles.join(', ');
            } catch (e) {
                console.error({ e });
            }
            user_data._first_order_date = data.user._first_order_date;
            console.log({ user_data })
            chargerBrevo(user_data, false);
            return;
        }

        // Visiteur non connecté : on lui demande son prénom avant d'ouvrir Brevo.
        if (!window.BrevoPrecollect) {
            // sécurité : sans le module, on retombe sur le comportement d'origine
            chargerBrevo(user_data, false);
            return;
        }

        ouvrirPrecollecte = window.BrevoPrecollect.init({
            onPret: (infos, ouvrirLeChat) => {
                ouvrirPrecollecte = null;
                chargerBrevo(Object.assign({}, user_data, {
                    email: infos.email || '',
                    firstName: infos.firstName,
                    lastName: infos.lastName || '',
                    phone: null,
                    notes: ''
                }), ouvrirLeChat);
            }
        });
    })
})
