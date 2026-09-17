(function () {
    var form = document.querySelector('#calresourceui .cr-delete-form');
    if (!form) {
        return;
    }
    form.addEventListener('submit', function (evt) {
        if (!window.confirm('Zeker weten? Dit verwijdert ook alles wat hieraan hangt.')) {
            evt.preventDefault();
        }
    });
})();
