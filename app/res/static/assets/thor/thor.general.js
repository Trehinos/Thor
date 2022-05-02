const confirmPost = (url, params, message, after) => {
    if (confirm(message)) {
        $.post(url, params, after);
    }
};

function loadPage(url, params, callback) {
    $("#btn-show-toasts").find(".fa-lg").removeClass("text-danger");
    let $page = $("#content");
    $page.html("<div id='page'><div style='padding-top: 48px; text-align: center;'><i class='fas fa-4x fa-spin fa-spinner'></i></div></div>");
    $page.load(url, params, (response) => {
        if (callback) {
            callback(response);
        }
    });
}