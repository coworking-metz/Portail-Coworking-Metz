document.addEventListener('DOMContentLoaded', () => {

    let notifications = document.querySelectorAll('.notification:not([data-visible])');

    notifications.forEach(notification => {
        const id = notification.dataset.id;
        const key = 'hide-notification-' + id
        if (sessionStorage.getItem(key)) {
            delete notification.dataset.visible
        } else {
            if(notification.dataset.once) {
                sessionStorage.setItem(key, true);
            }
            notification.dataset.visible = true;
            notification.querySelector('button').addEventListener('click', e => {
                delete notification.dataset.visible
                sessionStorage.setItem(key, true);
            })
        }
    });
})