document.addEventListener('DOMContentLoaded', () => {

    let notifications = document.querySelectorAll('.notification:not([data-visible])');

    removeQueryVar('notification');
    notifications.forEach(notification => {
        const id = notification.dataset.id;
        const key = 'hide-notification-' + id
        if (sessionStorage.getItem(key)) {

            delete notification.dataset.visible
        } else {
            if (notification.dataset.once) {
                sessionStorage.setItem(key, true);
            }
            notification.dataset.visible = true;
            notification.querySelector('button').addEventListener('click', e => {
                delete notification.dataset.visible
                sessionStorage.setItem(key, true);
            })
            if (notification.dataset.duration) {
                setTimeout(() =>
                    delete notification.dataset.visible
                    , notification.dataset.duration * 1000)
            }
        }
    });
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
})