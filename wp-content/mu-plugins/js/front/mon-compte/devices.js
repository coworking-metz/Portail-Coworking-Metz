document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('[data-action="ajouter-appareil"]').forEach(button => button.addEventListener('click', (e) => {
        document.querySelector('form[action="/mon-compte/appareils/"]').classList.toggle('hide')
        document.querySelector('.cta-devices').classList.toggle('hide')
    }))
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
                    inputField.value = mac;
                }
            });
        });
    }

    // Exemple d'utilisation
    watchMacInput();

})