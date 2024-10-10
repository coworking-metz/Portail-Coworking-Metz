document.addEventListener('DOMContentLoaded', () => {
    if (document.location.href.includes('wp-admin')) return;

    // document.querySelectorAll('[href="#ouvrir-brevo"]').forEach(bouton => bouton.addEventListener('click', e => {
    //     e.preventDefault()
    //     BrevoConversations('openChat', true);
    // }));
    document.addEventListener('click', e => {
        const target = e.target.closest('[href="#ouvrir-brevo"]')
        if (!target) return;
        e.preventDefault()
        Notifications.closeAll()
        BrevoConversations('openChat', true);
    });

    fetch('/mon-compte/?is-connected').then(response => response.json()).then(data => {
        const user_data = {}
        if (data.user) {
            user_data.hash = data.user.hash;
            if (data.user.ID) {
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
            }
            console.log({ user_data })
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
                    if (document.location.hash.includes('ouvrir-brevo')) {
                        BrevoConversations('openChat', true);
                    }
                })
                if (d.head) d.head.appendChild(s);
            })(document, window, 'BrevoConversations');



        }
    })
})