let toastList = [];

function showToasts() {
    $("#btn-show-toasts").find(".fa-lg").removeClass("text-danger");
    toastList.forEach((toast) => {
        toast.show();
    });
}

function initToasts() {
    let toastElList = [].slice.call(document.querySelectorAll('.toast'));
    toastList = toastElList.map(function (toastEl) {
        let toast = new bootstrap.Toast(toastEl);
        $("#btn-show-toasts").find(".fa-lg").removeClass("text-danger");
        toast.show();

        toastEl.addEventListener('hidden.bs.toast', () => {
            if ($(".toast.show").length < 1) {
                $("#btn-show-toasts").find(".fa-lg").addClass("text-danger");
            }
        });

        return toast;
    });
}
