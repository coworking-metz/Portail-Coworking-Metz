window.addEventListener('load', e => {
    console.log('pola')
    let form = document.querySelector('form#polaroid');
    if (form) {
        let fileUpload = form.querySelector('[name=photo]')
        fileUpload.addEventListener('change', e => {
            form.querySelector('button[type="submit"]').innerHTML='<i>Chargement en cours&hellip;</i>'
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
        // if (action == 'saisie-polaroid') {
        //     document.querySelector('.polaroid__generateur').dataset.saisie = true;
        //     polaroid_apercu();
        // }
    }))
    polaroid_apercu();


    function polaroid_apercu() {
        if (!document.querySelector('#polaroid_content')) return;
        const content = document.querySelector('#polaroid_content').value;
        let apercu = document.querySelector('.polaroid__apercu')
        apercu.querySelector('.photo').src = content;

        const polaroid_nom = document.querySelector('#polaroid_nom').value;
        document.querySelector(`[data-id="polaroid_nom"]`).innerHTML = polaroid_nom

        const polaroid_description = document.querySelector('#polaroid_description').value;
        document.querySelector(`[data-id="polaroid_description"]`).innerHTML = polaroid_description

        const polaroid_complement = document.querySelector('#polaroid_complement').value;
        document.querySelector(`[data-id="polaroid_complement"]`).innerHTML = polaroid_complement

    }
})