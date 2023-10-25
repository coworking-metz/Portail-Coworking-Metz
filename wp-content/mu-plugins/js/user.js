document.addEventListener('DOMContentLoaded', function () {
    ['fieldset-shipping', 'fieldset-billing'].forEach(id => {

        const fieldset = document.getElementById(id);
        console.log(fieldset);
        if (!fieldset) return;
        fieldset.classList.add('hidden')
        // Create the "See billing address" button
        const button = document.createElement('button');
        button.innerText = 'Voir/Modifier l\'adresse';
        button.classList.add('button')
        button.setAttribute('type', 'button')
        // Insert the button just before the fieldset
        fieldset.parentNode.insertBefore(button, fieldset);

        // Add event listener to the button
        button.addEventListener('click', function () {
            fieldset.classList.toggle('hidden')
        });
    })
});
