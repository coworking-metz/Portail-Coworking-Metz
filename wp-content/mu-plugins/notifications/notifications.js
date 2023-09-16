document.addEventListener('DOMContentLoaded', () => {

    let notifications = document.querySelectorAll('.notification:not([data-visible])');

    notifications.forEach(notification => {
        console.log(notification);
        notification.dataset.visible = true;
        notification.querySelector('button').addEventListener('click', e => {
            delete notification.dataset.visible
        })
    });
})