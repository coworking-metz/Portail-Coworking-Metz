/**
 * Pré-collecte d'identité pour les visiteurs non connectés.
 *
 * Tant que le visiteur ne s'est pas nommé, le widget Brevo n'est pas chargé :
 * on affiche à la place une bulle maison. Au clic, un petit panneau demande
 * prénom (requis), nom et e-mail. Les réponses sont gardées en localStorage,
 * puis transmises à Brevo pour que la conversation soit nominative côté
 * dashboard.
 *
 * Utilisation :
 *   BrevoPrecollect.init({ onPret: (infos, ouvrirLeChat) => { ... } })
 */
window.BrevoPrecollect = (function () {

    const CLE_STOCKAGE = 'brevo-visiteur';

    /* ---------- stockage ---------- */

    function lire() {
        try {
            const brut = window.localStorage.getItem(CLE_STOCKAGE);
            if (!brut) return null;
            const infos = JSON.parse(brut);
            // le prénom est la seule donnée indispensable
            if (!infos || !infos.firstName) return null;
            return infos;
        } catch (e) {
            // localStorage indisponible (navigation privée, cookies bloqués…)
            return null;
        }
    }

    function ecrire(infos) {
        try {
            window.localStorage.setItem(CLE_STOCKAGE, JSON.stringify(infos));
        } catch (e) {
            console.warn('BrevoPrecollect : stockage impossible', e);
        }
    }

    /* ---------- rendu ---------- */

    function creerBulle(auClic) {
        const bouton = document.createElement('button');
        bouton.type = 'button';
        bouton.className = 'brevo-pre-bulle';
        bouton.setAttribute('aria-label', 'Ouvrir la discussion');
        bouton.innerHTML =
            '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">' +
            '<path d="M12 3C6.99 3 3 6.36 3 10.5c0 2.3 1.23 4.35 3.16 5.72-.13 1.2-.6 2.3-1.4 3.2-.2.22-.06.58.24.58 1.9 0 3.5-.77 4.6-1.62.75.16 1.55.25 2.4.25 5.01 0 9-3.36 9-7.5S17.01 3 12 3z"/>' +
            '</svg>';
        bouton.addEventListener('click', auClic);
        document.body.appendChild(bouton);
        return bouton;
    }

    function creerPanneau(auValider, auFermer) {
        const panneau = document.createElement('div');
        panneau.className = 'brevo-pre-panneau';
        panneau.setAttribute('role', 'dialog');
        panneau.setAttribute('aria-modal', 'false');
        panneau.setAttribute('aria-labelledby', 'brevo-pre-titre');
        panneau.innerHTML =
            '<div class="brevo-pre-entete">' +
            '  <h2 id="brevo-pre-titre">Discutons&nbsp;!</h2>' +
            '  <p>Dites-nous qui vous êtes pour qu\'on puisse vous répondre.</p>' +
            '  <button type="button" class="brevo-pre-fermer" aria-label="Fermer">&times;</button>' +
            '</div>' +
            '<form class="brevo-pre-corps" novalidate>' +
            '  <div class="brevo-pre-champ">' +
            '    <label for="brevo-pre-prenom">Prénom <span aria-hidden="true">*</span></label>' +
            '    <input type="text" id="brevo-pre-prenom" name="firstName" autocomplete="given-name" required>' +
            '    <span class="brevo-pre-erreur" id="brevo-pre-erreur-prenom" hidden></span>' +
            '  </div>' +
            '  <div class="brevo-pre-champ">' +
            '    <label for="brevo-pre-nom">Nom</label>' +
            '    <input type="text" id="brevo-pre-nom" name="lastName" autocomplete="family-name">' +
            '  </div>' +
            '  <div class="brevo-pre-champ">' +
            '    <label for="brevo-pre-email">E-mail</label>' +
            '    <input type="email" id="brevo-pre-email" name="email" autocomplete="email">' +
            '    <span class="brevo-pre-erreur" id="brevo-pre-erreur-email" hidden></span>' +
            '  </div>' +
            '  <button type="submit" class="brevo-pre-envoyer">Démarrer la discussion</button>' +
            '</form>';

        const formulaire = panneau.querySelector('form');
        const prenom = panneau.querySelector('#brevo-pre-prenom');
        const nom = panneau.querySelector('#brevo-pre-nom');
        const email = panneau.querySelector('#brevo-pre-email');
        const erreurPrenom = panneau.querySelector('#brevo-pre-erreur-prenom');
        const erreurEmail = panneau.querySelector('#brevo-pre-erreur-email');

        function afficherErreur(champ, zone, message) {
            zone.textContent = message;
            zone.hidden = !message;
            champ.setAttribute('aria-invalid', message ? 'true' : 'false');
        }

        formulaire.addEventListener('submit', function (e) {
            e.preventDefault();

            const valeurPrenom = prenom.value.trim();
            const valeurEmail = email.value.trim();
            let valide = true;

            if (!valeurPrenom) {
                afficherErreur(prenom, erreurPrenom, 'Merci d\'indiquer votre prénom.');
                valide = false;
            } else {
                afficherErreur(prenom, erreurPrenom, '');
            }

            // l'e-mail est facultatif, mais s'il est saisi il doit être plausible
            if (valeurEmail && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valeurEmail)) {
                afficherErreur(email, erreurEmail, 'Cette adresse e-mail semble incorrecte.');
                valide = false;
            } else {
                afficherErreur(email, erreurEmail, '');
            }

            if (!valide) {
                (valeurPrenom ? email : prenom).focus();
                return;
            }

            auValider({
                firstName: valeurPrenom,
                lastName: nom.value.trim(),
                email: valeurEmail,
                date: new Date().toISOString()
            });
        });

        panneau.querySelector('.brevo-pre-fermer').addEventListener('click', auFermer);

        document.body.appendChild(panneau);
        prenom.focus();
        return panneau;
    }

    /* ---------- orchestration ---------- */

    function init(options) {
        const onPret = options.onPret;

        // Déjà identifié lors d'une visite précédente : on passe directement à Brevo.
        const connu = lire();
        if (connu) {
            onPret(connu, false);
            return null;
        }

        let bulle = null;
        let panneau = null;

        function fermerPanneau() {
            if (panneau) {
                panneau.remove();
                panneau = null;
            }
            if (bulle) bulle.focus();
        }

        function basculerPanneau() {
            if (panneau) {
                fermerPanneau();
                return;
            }
            panneau = creerPanneau(function (infos) {
                ecrire(infos);
                if (panneau) { panneau.remove(); panneau = null; }
                if (bulle) { bulle.remove(); bulle = null; }
                // true : on ouvre le chat Brevo dans la foulée, le visiteur
                // vient de cliquer pour parler à quelqu'un.
                onPret(infos, true);
            }, fermerPanneau);
        }

        function surEchap(e) {
            if (e.key === 'Escape' && panneau) fermerPanneau();
        }

        bulle = creerBulle(basculerPanneau);
        document.addEventListener('keydown', surEchap);

        // permet d'ouvrir le panneau depuis un lien [href="#ouvrir-brevo"]
        return function () {
            if (!panneau) basculerPanneau();
        };
    }

    return {
        init: init,
        lire: lire,
        CLE_STOCKAGE: CLE_STOCKAGE
    };
})();
