
const June = {
    $root: null,
    init: (selector) => {
        June.$root = $(selector);
    }
};

class JNode {

    static auto_id = 0;
    static SEPARATOR = "/";

    /**
     *
     * @param {string} name
     */
    constructor(name = "") {
        this.id = JNode.auto_id++;
        this.name = name;
        this.children = [];
    }

    /**
     * @param {JNode} node
     */
    add(node) {
        this.children.add(node);
        node._whenAdded(this);
    }

    /**
     * @param {function} callable
     * @param callArgs
     */
    each(callable, ...callArgs) {
        for (let child of this.children) {
            let callArgs = [];
            let c = callable(child, ...callArgs) ?? null;
            if (c !== null) {
                return c;
            }
        }

        return null;
    }

    /**
     *
     * @param {string} path
     *
     * @return {JNode|null}
     */
    find(path) {
        if (!path.includes(JNode.SEPARATOR)) {
            return this.children.find(node => node.name === path) ?? null;
        }

        const parts = path.split(JNode.SEPARATOR);
        const name = parts[0];
        path = parts.slice(1).join(JNode.SEPARATOR);

        return this.each((child) => {
            if (child.name === name) {
                return child.find(path);
            }
        });
    }

    /**
     * @param {JNode|string} nodeOrPath
     */
    remove(nodeOrPath) {
        let node = nodeOrPath;
        if (!(nodeOrPath instanceof JNode)) {
            node = this.find(nodeOrPath);
        }

        const index = this.children.indexOf(node);
        if (index !== -1) {
            this.children[index] = null;
            delete this.children[index];
        }
        node._whenRemoved(this);
    }

    /**
     * @return {string}
     */
    _draw() {
        return "";
    }

    display() {
        this._draw();
        this._whenDisplayed();
    }

    _whenAdded(parent) {}
    _whenDisplayed() {}
    _whenRemoved(parent) {}

}

class Text extends JNode
{

    constructor(text) {
        super("Text:" + text);
        this.text = text;
    }

    draw() {
        return this.text;
    }

}

class Container extends JNode
{

    /**
     * @return {string}
     */
    _draw() {
        return this.drawChildren();
    }

    drawChildren() {
        let output = "";
        for (const child of this.children) {
            output += child.draw();
        }
        return output;
    }

}

class HtmlDiv extends Container {

    constructor(name, htmlId = null) {
        super("div:" + name);
        this.htmlId = htmlId ?? ("div-" + this.id);
    }

    _draw() {
        return `<div id="${this.id}">` + this.drawChildren() + "</div>";
    }

}

class Panel extends Container {

    constructor(name, htmlId = null) {
        super("div:" + name);
        this.htmlId = htmlId ?? ("div-" + this.id);
    }

    _draw() {
        return `<div id="${this.id}" class="alert alert-primary">` + this.drawChildren() + "</div>";
    }

}

$(() => {
    const container = new Panel("test");
    container.add(new Text("TEST"));

});
