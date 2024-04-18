window.addEventListener('load', e => {
    const fieldset = document.querySelector('fieldset:has(.password-input)');
    console.log({ fieldset })
    if (fieldset) {
        fieldset.addEventListener('click', e => {
            document.body.dataset.editPassword = true;
        })
    }

    const date_naissance = document.querySelector('p.form-row:has(#date_naissance)')

    if (date_naissance) {
        const cible = document.querySelector('p.form-row:has(#account_display_name)');
        if (cible) {
            cible.parentNode.insertBefore(date_naissance, cible);
        }
    }
})