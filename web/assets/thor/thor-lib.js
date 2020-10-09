const ModalSelector = "#modal";

const Modal = {
    $elem: $(ModalSelector),
    title: (t) => {
        Modal.$elem.find(".modal-title").text(t);
        return Modal;
    },
    body: (b) => {
        Modal.$elem.find(".modal-body").html(b);
        return Modal;
    },
    footer: (f) => {
        Modal.$elem.find(".modal-footer").html(f);
        return Modal;
    },
    open: () => {
        Modal.$elem.modal("show");
        return Modal;
    },
    close: () => {
        Modal.$elem.modal("hide");
        return Modal;
    },
    loadBody: (url, data) => {
        $.get(url, data, (response) => {
            Modal.body(response).open();
        });
        return Modal;
    },
    load: (url, data) => {
        $.get(url, data, (response) => {
            Modal.$elem.html(response);
            Modal.open();
        });
        return Modal;
    }
};

