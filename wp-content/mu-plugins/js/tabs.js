document.addEventListener('DOMContentLoaded', function () {
    let wraps = document.querySelectorAll('.acf-tab-wrap')

    wraps.forEach(wrap => {
        wrap.querySelectorAll(' :scope > ul > li > a').forEach(a => {
            a.addEventListener('click', function (e) {
                document.location.hash = '#tab-' + a.dataset.key;
            });
        });
    });

    if(document.location.hash) {
        let key = document.location.hash.replace('#', '').replace('tab-', '');
        let a = this.querySelector(`.acf-tab-wrap a[data-key="${key}"]`);
        if(a) {
            a.click();
        }
    }
});