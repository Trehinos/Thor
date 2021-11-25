function $(selector) {
    return document.querySelector(selector);
}

function $$(selector) {
    return document.querySelectorAll(selector);
}

function $$each(selector, func) {
    $$(selector).forEach(func);
}

function EVENT(element, event, func) {
    element.addEventListener(event, func);
}

function AJAX(method, url, data, success, fail) {
    let urlObject = new URL(url);
    let body = null;
    if (method === 'GET') {
        for (let param in data) {
            urlObject.searchParams.append(param, data[param]);
        }
    } else {
        body = JSON.stringify(data);
    }
    fetch(urlObject.toString(), {method: method, body: body}).then((response) => {
        if (response.ok) {
            response.blob().then((blob) => {
                if (success) {
                    blob.text().then((text) => {
                        success(text);
                    });
                }
            });
        }
    }).catch((error) => {
        if (fail) {
            fail(error);
        } else {
            console.error("AJAX Error : " + error.message);
        }
    });
}

function $load(selector, url, data, after) {
    AJAX('GET', url, data, (responseText) => {
        $(selector).innerHTML = responseText;
        if (after) {
            after(responseText);
        }
    });
}

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

// TODO rewrite
const confirmPost = (url, params, message, after) => {
    if (confirm(message)) {
        $.post(url, params, after);
    }
};

function loadPage(url, params, callback) {
    let $page = $("#content");
    $page.innerHTML = "<div id='page'><div style='padding-top: 48px; text-align: center;'><i class='fas fa-4x fa-spin fa-spinner'></i></div></div>";
    $load("#content", url, params, (response) => {
        if (callback) {
            callback(response);
        }
    });
}
