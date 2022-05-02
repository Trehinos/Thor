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
            func, modifiers: {shift, ctrl, alt}
        };
    }
};

$(() => {
    $(document).on("keydown", KeyMapper.onKeyDown);
    KeyMapper.bindKey("F1", () => displayHelp());
    KeyMapper.bindKey("CTRL+F2", () => menuClick($("#btn-index-page")));
    KeyMapper.bindKey("CTRL+F3", () => menuClick($("#btn-users")));
});
