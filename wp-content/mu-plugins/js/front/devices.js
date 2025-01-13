
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

    /**
     * Vérifie si une adresse MAC est probablement randomisée
     * @param {string} mac - L'adresse MAC à vérifier
     * @return {boolean} True si l'adresse MAC est probablement randomisée, false sinon
     */
    function isMacAddressRandomized(mac) {
        // Normalise l'adresse MAC en supprimant les caractères non hexadécimaux
        const cleanedMac = mac.replace(/[^a-fA-F0-9]/g, '').toUpperCase();

        // Récupère le deuxième caractère de l'adresse MAC nettoyée
        const secondChar = cleanedMac[1];

        // Définit les caractères qui indiquent une adresse randomisée
        const randomizedChars = ['2', '6', 'A', 'E'];

        // Vérifie si le deuxième caractère est un de ceux qui indiquent une randomisation
        return randomizedChars.includes(secondChar);
    }

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
                // if (macInput && isMacAddressRandomized(macAddress)) {
                //     Notifications.generate({
                //         position: 'top-right',
                //         type: 'warning',
                //         titre: 'Problème d\'identification de votre appareil',
                //         texte: `Il semble que vote appareil utilise une technologie de sécurité incompatible avec le système de détection des présences du coworking. <a href="https://www.coworking-metz.fr/comment-desactiver-les-adresses-mac-aleatoires/" target="_blank">En savoir plus</a>`,
                //         cta: {
                //             url: `#ouvrir-brevo`,
                //             caption: 'Demander de l\'aide'
                //         }
                //     });

                //     return;
                // }
                if (macDejaAssociee(macAddress)) {
                    return;
                }
				document.querySelector('[name="adresse-mac"]').value = macAddress
					document.querySelector('[data-action="ajouter-appareil"]').click();

                let message = "Voulez-vous ajouter l'appareil que vous être en train d'utiliser à votre compte Coworking Metz ?"
                if (!form && coworkingDevices.length == 0) {
                    message = "Vous n'avez pas encore associé d'appareil à votre compte. Voulez vous ajouter celui que vous utilisez actuellement ?"
                }
                Notifications.generate({ storage: 'local', id: macAddress, position: 'top-right', type: 'success', titre: 'Nouvel appareil détecté !', texte: message + " <a href='/mon-compte/appareils/'>En savoir plus</a>", cta: { url: `/mon-compte/appareils/?adresse-mac=${macAddress}`, caption: 'Oui, ajouter cet appareil !' } })
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