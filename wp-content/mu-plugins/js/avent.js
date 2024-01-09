document.addEventListener('DOMContentLoaded', e => {
    let avent_tirages = document.querySelector('[data-name="avent_tirages"] textarea');

    if (!avent_tirages) return;

    fetch('/api-json-wp/cowo/v1/avent_tirages', {
        method: 'POST', // Specify the method
        headers: {
            'Content-Type': 'application/json' // Specify the content type
        },
        body: JSON.stringify({ value: avent_tirages.value }) // Convert the data object to a JSON string
    })
        .then(response => response.json()) // Parsing the JSON response
        .then(data => {
            const html = []
            console.log(data)
            data.forEach(element => {
                html.push(`<div><b>${element.date}</b> - <a href="user-edit.php?user_id=${element.user.id}">${element.user.name} (${element.user.email})</a></div>`);
            });
            document.querySelector('[data-name="avent_tirages"] .acf-label').innerHTML += html.join('');
        });
    console.log(avent_tirages.value)
})