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

let toastList = [];

const KeyMapper = {
    SHIFT: "SHIFT",
    CTRL: "CTRL",
    ALT: "ALT",
    KEY_ONLY: "",
    bindings: {},
    onKeyDown: (e) => {
        console.log(e.key);
        if (KeyMapper.bindings.hasOwnProperty(e.key)) {
            if (
                KeyMapper.bindings[e.key].modifiers.shift && !e.shiftKey ||
                KeyMapper.bindings[e.key].modifiers.ctrl && !e.ctrlKey ||
                KeyMapper.bindings[e.key].modifiers.alt && !e.altKey
            ) {
                return true;
            }
            e.preventDefault();
            KeyMapper.bindings[e.key].func();
            return false;
        }
    },
    bindKey: (key, func) => {
        let shift = false;
        let ctrl = false;
        let alt = false;
        key.split("+").forEach((current) => {
            switch (current.toUpperCase()) {
                case KeyMapper.SHIFT: shift = true; break;
                case KeyMapper.CTRL: ctrl = true; break;
                case KeyMapper.ALT: alt = true; break;
                default: key = current;
            }
        });
        KeyMapper.bindings[key] = {
            func, modifiers: {shift, ctrl, alt, none: !(shift || ctrl || alt)}
        };
    }
};

/**
 * @param key:string
 * @param func:function|null
 */

$(() => {
    $(document).on("keydown", KeyMapper.onKeyDown);
    KeyMapper.bindKey("F1", () => displayHelp());
    KeyMapper.bindKey("CTRL+F2", () => menuClick($("#btn-index-page")));
    KeyMapper.bindKey("CTRL+F3", () => menuClick($("#btn-users")));
});

const confirmPost = (url, params, message, after) => {
    if (confirm(message)) {
        $.post(url, params, after);
    }
};

function displayHelp() {
    Modal.load(helpUrl);
}

function loadPage(url, params, callback) {
    let $page = $("#content");
    $page.html("<div id='page'><div style='padding-top: 48px; text-align: center;'><i class='fas fa-4x fa-spin fa-spinner'></i></div></div>");
    $page.load(url, params, (response) => {
        if (callback) {
            callback(response);
        }
    });
}

function showToasts() {
    toastList.forEach((toast) => {
        toast.show();
    });
}
