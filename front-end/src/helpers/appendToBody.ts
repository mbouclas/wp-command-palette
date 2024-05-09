export function appendToBody(node: HTMLElement) {
    document.body.appendChild(node);

    return {
        destroy() {
            document.body.removeChild(node);
        }
    };
}
