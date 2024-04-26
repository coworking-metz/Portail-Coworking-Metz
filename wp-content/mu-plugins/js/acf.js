window.addEventListener('load', function () {
    const checkbox = document.querySelector('[data-name="importer_depuis_github"] input[type=checkbox]');
    if (!checkbox) return;
    console.log('importer_depuis_github', checkbox)
    // Function to toggle the visibility of the content editor
    function toggleContentEditor() {
        const toHide = document.querySelectorAll('#postdivrich, #wpb_wpbakery, #redux-liquid_one_opt-metabox-liquid-page-options, .composer-switch');
        toHide.forEach(item => {
            console.log(item)
            if (checkbox.checked) {
                item.dataset.hidden=true
            } else {
                delete item.dataset.hidden
            }
        })
    }

    setTimeout(toggleContentEditor, 500)

    // Bind the function to the change event of the 'depuis_github' checkbox
    checkbox.addEventListener('change', toggleContentEditor);
});
