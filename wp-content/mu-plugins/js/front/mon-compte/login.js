document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form.woocommerce-form-login')
    if (!form) return;

    const params = new URLSearchParams(window.location.search);
    const login = params.get('login');
    if (login) {
        document.getElementById('username').value = login;
    }
    
    form.addEventListener('submit', (e) => {
        form.dataset.loading = true
        const button = form.querySelector('button');
        if (!button) return;
        setTimeout(() => button.disabled = true, 500);
    })
})