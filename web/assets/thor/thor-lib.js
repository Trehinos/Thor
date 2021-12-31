const Modal = {
    $elem: null,
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
        Modal.open();
        Modal.$elem.html("<div id='page'><div style='padding-top: 96px; text-align: center;'><i class='fas fa-2x fa-spin fa-spinner text-info'></i></div></div>");
        $.get(url, data, (response) => {
            Modal.$elem.html(response);
        });
        return Modal;
    },
    message: (title, message) => {
        Modal.title(title).body(message).open();
        return Modal;
    }
};

const confirmPost = (url, params, message, after) => {
    if (confirm(message)) {
        $.post(url, params, after);
    }
};

function loadPage(url, params, callback) {
    let $page = $("#content");
    $page.html("<div id='page'><div style='padding-top: 48px; text-align: center;'><i class='fas fa-4x fa-spin fa-spinner'></i></div></div>");
    $page.load(url, params, (response) => {
        if (callback) {
            callback(response);
        }
    });
}
