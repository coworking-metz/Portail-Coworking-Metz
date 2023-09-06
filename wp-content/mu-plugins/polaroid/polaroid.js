window.addEventListener('load', e => {
    console.log('pola')

    let form = document.querySelector('form#polaroid');
    if (form) {
        let fileUpload = form.querySelector('[name=photo]')
        fileUpload.addEventListener('change', e => {
            form.submit();
        })
    }

    let formPola = document.querySelector('form#saisie-polaroid');

    if (formPola) {
        let pointer = false;
        formPola.querySelectorAll('input').forEach(input => input.addEventListener('input', e => {
            clearTimeout(pointer);
            pointer = setTimeout(polaroid_apercu, 1000);
        }))
    }

    document.querySelectorAll('[data-action]').forEach(button => button.addEventListener('click', e => {
        let action = e.target.closest('[data-action]').dataset.action;
        if (!action) retun;
        if (action == 'saisie-polaroid') {
            document.querySelector('.polaroid__generateur').dataset.saisie = true;
            polaroid_apercu();
        }
    }))

    function polaroid_apercu() {
        let queryString = [Math.random()];
        let formElements = formPola.elements;
        let bouton = document.querySelector('[name="valider-polaroid"]');
        for (let i = 0; i < formElements.length; i++) {
            if (formElements[i].tagName === 'INPUT' && formElements[i].name) {
                let key = encodeURIComponent(formElements[i].name);
                let value = encodeURIComponent(formElements[i].value);
                queryString.push(key + '=' + value);
            }
        }

        queryString = queryString.join('&');
        let apercu = document.querySelector('.polaroid__apercu')
        apercu.dataset.loading = true;
        bouton.disabled = true;
        apercu.innerHTML = '';
        let img = document.createElement('img');

        img.addEventListener('load', () => {
            delete apercu.dataset.loading
            bouton.disabled = false;
        });
        img.src = '/polaroid/?' + queryString;
        apercu.appendChild(img);
    }
})