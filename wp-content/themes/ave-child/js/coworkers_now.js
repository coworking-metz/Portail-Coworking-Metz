document.addEventListener('DOMContentLoaded', function () {
	if (!document.querySelector('#text-count-coworker')) return;
	fetch('https://wpapi.coworking-metz.fr/api-json-wp/cowo/v1/coworkers_now')
		.then(response => response.json()).then(response => {
			console.log(response);
			document.querySelector('#text-count-coworker').innerHTML = response.content;
		})
})