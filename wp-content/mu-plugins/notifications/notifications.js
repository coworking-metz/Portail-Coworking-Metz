const Notifications = {}
document.addEventListener('DOMContentLoaded', () => {

    activerNotifications()

    function activerNotifications() {
        let notifications = document.querySelectorAll('.notification:not([data-visible])');

        removeQueryVar('notification');
        notifications.forEach(notification => {
            const storage = notification.dataset.storage == 'local' ? localStorage : sessionStorage;
            const id = notification.dataset.id;
            const key = 'hide-notification-' + id
            if (storage.getItem(key)) {

                delete notification.dataset.visible
            } else {
                if (notification.dataset.once) {
                    storage.setItem(key, true);
                }
                notification.dataset.visible = true;
                notification.querySelector('button').addEventListener('click', e => {
                    delete notification.dataset.visible
                    storage.setItem(key, true);
                })
                if (notification.dataset.duration) {
                    setTimeout(() =>
                        delete notification.dataset.visible
                        , notification.dataset.duration * 1000)
                }
            }
        });

    }
    /**
 * Supprimer le paramètre de notification de l'URL sans recharger la page
 */
    function removeQueryVar(name) {
        const url = new URL(window.location.href);
        const searchParams = url.searchParams;

        // Vérifier si le paramètre de notification existe
        if (searchParams.has(name)) {
            // Supprimer le paramètre de notification
            searchParams.delete(name);

            // Mettre à jour l'URL sans recharger la page
            const newUrl = url.origin + url.pathname + (searchParams.toString() ? '?' + searchParams.toString() : '');
            window.history.replaceState({}, document.title, newUrl);
        }
    }

    function generateNotification(data) {


        const id = 'notification-' + (data.id || Number(Math.random() * 100));
        console.log('[data-id="' + id + "']")
        if (document.querySelector('[data-id="' + id + '"]')) return;
        let cta = '';
        if (data.cta) {
            cta = `<span class="cta"><a href="${data.cta.url}" class="button">${data.cta.caption}</a></span>`;
        }
        if (data.temporaire) {
            data.duree = 5;
        }

        const notificationHTML = `<div class="notification ${data.position || ''}" role="alert" data-id="${id}" data-type="${data.type || 'default'}" data-once="${data.once ? 'true' : ''}" data-storage="${data.storage ? data.storage : 'session'}" data-duration="${data.duree || ''}">
        <div>
        <div>
        <figure><img src="${data.image || ''}"></figure>
        <p><b class="titre">${data.titre}</b><span>${data.texte}</span></p>
        </div>
        ${cta}
        </div>
        <button>&#x2716;</button>
      </div>`;

        // Inject the notification into the DOM before the closing </body> tag
        document.body.insertAdjacentHTML('beforeend', notificationHTML);
        setTimeout(() => activerNotifications(), 500)
        return id;
    }


    Notifications.generate = generateNotification
    Notifications.closeAll = function () {
        document.querySelectorAll('.notification[data-visible]').forEach(div => div.querySelector('button').click())
    }
})