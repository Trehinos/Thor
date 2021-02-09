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
        Modal.$elem.html("<div id='page'><div style='padding-top: 96px; text-align: center;'><i class='fas fa-2x fa-spin fa-spinner text-info'></i></div></div>");
        Modal.open();
        $.get(url, data, (response) => {
            Modal.$elem.html(response);
        });
        return Modal;
    }
};

const confirmPost = (url, params, message, after) => {
    if (confirm(message)) {
        $.post(url, params, after);
    }
};
