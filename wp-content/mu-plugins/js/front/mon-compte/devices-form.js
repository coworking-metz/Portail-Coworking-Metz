document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[action="/mon-compte/appareils/"]');
    const macInput = document.querySelector('[name="adresse-mac"]');

    if (!form) return;

    document.querySelectorAll('[data-action="ajouter-appareil"]').forEach(button => button.addEventListener('click', (e) => {
        toggleForm()
    }))
    document.querySelectorAll('[data-action="annuler-ajouter-appareil"]').forEach(button => button.addEventListener('click', (e) => {
        macInput.value = ''
        toggleForm()
    }));

    function toggleForm(macAddress = false) {
        if (!form) return;
        if (macAddress) {
            macInput.value = macAddress;
        }
        form.classList.toggle('hide')
        document.querySelector('.cta-devices').classList.toggle('hide')
    }


    // Fonction pour écouter les changements d'entrée et formater en adresse MAC
    function watchMacInput() {
        const macInputFields = document.querySelectorAll('input[name="adresse-mac"]');

        macInputFields.forEach((inputField) => {
            inputField.addEventListener('input', function () {
                let mac = inputField.value
                    .replace(/\s+/g, '') // Supprimer les espaces
                    .replace(/[^a-fA-F0-9]/g, '') // Supprimer tout ce qui n'est pas un chiffre ou une lettre A-F
                    .toUpperCase() // Convertir en majuscules
                    .match(/.{1,2}/g) // Diviser en groupes de 2 caractères
                    ?.join(':'); // Ajouter un tiret entre chaque groupe de 2

                if (mac) {
                    // Limiter la longueur à 17 caractères (maximum pour une adresse MAC valide)
                    mac = mac.substring(0, 17);
                    inputField.value = mac;
                }
            });
        });
    }


    // Exemple d'utilisation
    watchMacInput();

})