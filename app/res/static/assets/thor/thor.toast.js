let toastList = [];

function showToasts() {
    $("#btn-show-toasts").removeClass("btn-danger").addClass("btn-dark");
    toastList.forEach((toast) => {
        toast.show();
    });
}

function initToasts() {
    let toastElList = [].slice.call(document.querySelectorAll('.toast'));
    toastList = toastElList.map(function (toastEl) {
        let toast = new bootstrap.Toast(toastEl);
        $("#btn-show-toasts").removeClass("btn-danger").addClass("btn-dark");
        toast.show();

        toastEl.addEventListener('hidden.bs.toast', () => {
            if ($(".toast.show").length < 1) {
                $("#btn-show-toasts").removeClass("btn-dark").addClass("btn-danger");
            }
        });

        return toast;
    });
}
