document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.product-template-default')) {
        const dateDebut = document.querySelector("#tm-extra-product-options");
        if (dateDebut) {
            const dateField = dateDebut.querySelector('input');
            dateDebut.addEventListener('click', e => {
                const a = e.target.closest('a');
                if (!a) return;
                e.preventDefault();
                const href = a.getAttribute('href').replace('#', '');
                if (href == 'date-yesterday') dateField.value = getYesterday()
                if (href == 'date-today') dateField.value = getToday()
                if (href == 'date-tomorrow') dateField.value = getTomorrow()
            })
            // const btnAddToCart = document.querySelector(".single_add_to_cart_button");
            // dateDebut.appendChild(btnAddToCart); POURQUOI ??

            const dateConseillee = getDateFromQuery()
            if(dateConseillee) {
                dateField.value = dateConseillee
                const div = document.createElement('div')
                div.setAttribute('style','font-size:.7em;line-height:1.1')
                div.innerHTML = `Vous devez commencer votre nouvel abonnement le ${dateConseillee} pour correspondre avec la fin de votre abonnement précédent, ou de votre solde de tickets`;
                dateField.parentElement.after(div)
    
            } else {
                dateField.value = getToday()
                const div = document.createElement('div')
                div.innerHTML = `<a href="#date-yesterday">Hier</a> - <a href="#date-today">Aujourd'hui</a> - <a href="#date-tomorrow">Demain</a>`;
                dateField.parentElement.after(div)
    
            }
        }
    }


    function getDateFromQuery() {
        const queryParams = new URLSearchParams(window.location.search);
        const startDate = queryParams.get('startDate');
        const dateField = document.querySelector('#dateField'); // Ensure the ID matches your form field's ID

        let dateValue = new Date();

        if (startDate) {
            const [year, month, day] = startDate.split('-');
            dateValue = new Date(`${year}-${month}-${day}`);

            const formattedDate = dateValue.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });
            return formattedDate;
        }
    }

    function getToday() {
        const today = new Date();
        return today.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }
    function getTomorrow() {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        return tomorrow.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }
    function getYesterday() {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() - 1);
        return tomorrow.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

})