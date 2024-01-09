document.addEventListener('DOMContentLoaded', function () {
    fetch('https://www.coworking-metz.fr/events/ping.php').then(response => response.text()).then(response => {
        console.log('events', response)
    })
})