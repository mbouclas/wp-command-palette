import libstyles from "../app.css?inline"

export function attachStylesToShadowRoot(elementName: string) {
    const el = document.querySelector(elementName);
    if (!el) {
        return;
    }

    const shadow = el.shadowRoot;
    if (!shadow) {
        return;
    }

    const existing = shadow.querySelector('link');
    if (existing) {
       return
    }

    let styles = document.createElement('style');
    styles.innerHTML = libstyles;
    shadow.appendChild(styles);

}
