document.addEventListener('DOMContentLoaded', () => {
    const avatar = document.querySelector('#my-account-menu .user-avatar img').addEventListener('click', () => {
        document.location.href='/mon-compte/polaroid/'
    })
});