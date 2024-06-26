
// compte.js
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
// coworkers_now.js
document.addEventListener('DOMContentLoaded', function () {
    if (!document.querySelector('#text-count-coworker')) return;
    fetch(WP_API_URL + '/api-json-wp/cowo/v1/coworkers_now')
        .then(response => response.json()).then(response => {
            console.log(response);
            document.querySelector('#text-count-coworker').innerHTML = response.content;
        })
})
// stats.js
document.addEventListener('DOMContentLoaded', function () {
    if (!document.querySelector('body.home')) return;

    // Event listener for scroll
    window.addEventListener('scroll', watchScroll);

    function watchScroll() {
        const scrolledHeight = window.scrollY;
        const viewportHeight = window.innerHeight;

        if (scrolledHeight >= 2 * viewportHeight) {
            loadStats();
            window.removeEventListener('scroll', watchScroll);
        }
    }
    function loadStats() {
        if (!document.querySelector("#coworkers-veille")) return;
        fetch(WP_API_URL + "/api-json-wp/cowo/v1/stats")
            .then(response => response.json())
            .then(data => {
                console.log(data)
                const nbCoworkerVeille = document.querySelector("#coworkers-veille");
                const nbCoworkersSemainePrecedente = document.querySelector("#coworkers-semaine-precedente");
                const nbCoworkersMoisPrecedent = document.querySelector("#coworkers-mois-precedent");
                const nbCoworkersAnneePrecedente = document.querySelector("#coworkers-annee-precedente");
                const nbCoworkersDebut = document.querySelector("#coworkers-debut");
                const nbJoursCoworkesSemaine = document.querySelector("#jours-coworkes-semaine");
                const nbJoursCoworkesMois = document.querySelector("#jours-coworkes-mois");
                const nbJoursCoworkesAnnee = document.querySelector("#jours-coworkes-annee");
                const nbJoursCoworkesDebut = document.querySelector("#jours-coworkes-debut");

                nbCoworkerVeille.textContent = data.nb_coworkers_veille;
                nbCoworkersSemainePrecedente.textContent = data.nb_coworkers_semaine_precedente;
                nbCoworkersMoisPrecedent.textContent = data.nb_coworkers_mois_precedent;
                nbCoworkersAnneePrecedente.textContent = data.nb_coworkers_annee_precedente;
                nbCoworkersDebut.textContent = data.nb_coworkers_debut;
                nbJoursCoworkesSemaine.textContent = Math.floor(data.nb_jours_presence_semaine_precedente);
                nbJoursCoworkesMois.textContent = Math.floor(data.nb_jours_presence_mois_precedent);
                nbJoursCoworkesAnnee.textContent = Math.floor(data.nb_jours_presence_annee_precedente);
                nbJoursCoworkesDebut.textContent = Math.floor(data.nb_jours_presence_debut);
            });
        const currentYear = new Date().getFullYear();
        const lastYear = currentYear - 1;
        const anneePrecedente = document.querySelectorAll(".annee-precedente");
        anneePrecedente.textContent = lastYear;

        if (document.querySelector('#count-coworker')) {
            let countCoworker = document.querySelector('#count-coworker').innerText;
            let textCountCoworker = document.querySelector('#text-count-coworker');
            if (countCoworker == 0) {
                textCountCoworker.innerText = 'Il n\'y a actuellement aucun coworker !';
            }
        }
    }
});