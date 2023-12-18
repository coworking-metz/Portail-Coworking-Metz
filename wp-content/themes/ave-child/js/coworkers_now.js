document.addEventListener('DOMContentLoaded', function () {
    fetch('https://api.coworking-metz.fr/api-json-wp/cowo/v1/coworkers_now').then(response => response.json()).then(response => {
        console.log(response);
        document.querySelector('#text-count-coworker').innerHTML = response.content;
    })
})