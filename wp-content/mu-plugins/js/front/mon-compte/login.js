document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form.woocommerce-form-login')
    if (!form) return;
    form.addEventListener('submit', (e) => {
        form.dataset.loading = true
        const button = form.querySelector('button');
        if (!button) return;
        setTimeout(() => button.disabled = true, 500);
    })
})