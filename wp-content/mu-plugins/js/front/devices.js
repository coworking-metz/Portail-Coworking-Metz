
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[action="/mon-compte/appareils/"]');
    const macInput = document.querySelector('[name="adresse-mac"]');

    if (typeof coworkingDevices != 'undefined' && coworkingNbOrders > 0) {
        fetchProbe()
    }


    if (!form) return;


    form.addEventListener('submit', e => {
        if (macDejaAssociee(macInput.value)) {
            alert('Cet appareil est déjà associé à votre compte')
            e.preventDefault()
            return false;
        }
    })


    function macDejaAssociee(macAddress) {
        for (const device of coworkingDevices) {
            if (device.macAddress == macAddress) return true;
        }
        // return document.querySelector('[data-mac="' + macAddress + '"]') ? true : false
    }
    function fetchProbe() {

        if (macInput && macInput.value) return;


        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 3000);

        fetch('https://probe.coworking-metz.fr/info', {
            cache: 'no-cache',
            signal: controller.signal
        })
            .then(response => response.json())
            .then(data => {
                console.log({ data })
                clearTimeout(timeoutId);
                const macAddress = data.device.macAddress;
                if (!macAddress) {
                    return;
                }
                if (macDejaAssociee(macAddress)) {
                    return;
                }

                let message = "Voulez-vous ajouter l'appareil que vous être en train d'utiliser à votre compte Coworking Metz ?"
                if (!form && coworkingDevices.length == 0) {
                    message = "Vous n'avez pas encore associé d'appareil à votre compte. Voulez vous ajouter celui que vous utilisez actuellement ?"
                }
                Notifications.generate({ storage:'local', id: macAddress, position: 'top-right', type: 'success', titre: 'Nouvel appareil détecté !', texte: message + " <a href='/mon-compte/appareils/'>En savoir plus</a>", cta: { url: `/mon-compte/appareils/?adresse-mac=${macAddress}`, caption: 'Oui, ajouter cet appareil !' } })
            })
            .catch(error => {
                if (error.name === 'AbortError') {
                    console.error('Request timed out');
                } else {
                    console.error('Fetch error:', error);
                }
            });

    }
})