document.addEventListener('DOMContentLoaded', function () {

    const selectFields = document.querySelectorAll('[data-name*="email_"][data-type="select"] select');
    selectFields.forEach(function (select) {
        console.log(select);

        const linkVoir = document.createElement('a');
        linkVoir.target = '_blank';
        linkVoir.innerText = 'Voir ce template';
        select.parentNode.appendChild(linkVoir);

        function updateLinkVoir() {
            const value = this.value;
            if (this.value) {
                linkVoir.classList.remove('hidden')
                linkVoir.href = `/wp-admin/?template_preview=${value}`;
            } else {
                linkVoir.classList.add('hidden')
            }
        }

        // Lien initial
        updateLinkVoir.call(select);

        const span = document.createElement('span');
        span.innerHTML = ' &nbsp; ';
        select.parentNode.appendChild(span);

        const linkModifier = document.createElement('a');
        linkModifier.target = '_blank';
        linkModifier.innerText = 'Modifier ce template';
        select.parentNode.appendChild(linkModifier);

        function updateLinkModifier() {
            const value = this.value;
            if(value.includes('brevo-')) {
                const bid = value.replace('brevo-','');
                linkModifier.classList.remove('hidden')
                linkModifier.href = `https://my.brevo.com/camp/template/${bid}/message-setup?editor=v6`;

            } else if (this.value > 0) {
                linkModifier.classList.remove('hidden')
                linkModifier.href = `post.php?post=${value}&action=edit&classic-editor`;
            } else {
                linkModifier.classList.add('hidden')
            }

        }

        // Lien initial
        updateLinkModifier.call(select);

        select.addEventListener('change', function () {
            // Mettre à jour le lien lors du changement de sélection
            updateLinkVoir.call(this);
            updateLinkModifier.call(this);
        });
    });
});