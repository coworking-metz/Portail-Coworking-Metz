document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('[href="/wp-admin/users.php?export-users&voting"]').addEventListener('click', e => {
        e.preventDefault()
        let date;
        date  = prompt('Vous allez télécharger la liste des membres électeurs pour une date donnée. Attention, si la date est dand le futur, le résultat sera une projection en fonction des données de fréquentation des coworkers.\nEntrez la date de référecnce (DD/MM/YYYY)','aujourd\'hui')

        if(date) {
            window.open('/wp-admin/users.php?export-users&voting&date='+date, '_self');
        }
    })
})