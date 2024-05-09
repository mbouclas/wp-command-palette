<script lang="ts">
    import {
        setClosedWindowAction, setMaximizedWindowAction,
        setMinimizedWindowAction, setWindowDimensionsAction,
        taskbarStore,
        TaskBarWindow, windowExists
    } from "../stores/taskbar.store";
    import {onMount} from "svelte";
    export let windowInstance: TaskBarWindow;
    export let type: 'iframe' | 'local' = 'local';
    let currentWindow: HTMLElement | null;
    let el: HTMLElement | null;



    onMount(() => {
        setTimeout(() => {
            init();
            // console.log(windowInstance)
        }, 0);
    })

    function init() {

        el = document.querySelector('command-palette');
        if (!el) {
            console.log('Element not found')
            return;
        }
        const shadow = el.shadowRoot;
        if (!shadow) {
            console.log('Shadow not found')
            return;
        }
        const win = shadow.getElementById(windowInstance.id);

        if (!win) {
            console.log('Window not found')
            return;
        }
        currentWindow = win;
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        // Ensure the window fits within the viewport dimensions
        const maxLeft = viewportWidth - win.offsetWidth;
        const maxTop = (viewportHeight / 2) - (win.offsetHeight - 150);

        // Generate random positions
        const randomLeft = windowInstance.dimensions.left || Math.floor(Math.random() * maxLeft);
        const randomTop = windowInstance.dimensions.top || Math.floor(Math.random() * maxTop);

        // Set the window position
        win.style.left = `${randomLeft}px`;
        win.style.top = `${randomTop}px`;
        win.style.width = `${windowInstance.dimensions.width}px`;
        win.style.height = `${windowInstance.dimensions.height}px`;

        // Assuming you have multiple windows, each with a unique ID
        const windows = shadow.querySelectorAll('.window');

        windows.forEach(win => {
            makeWindowResizableAndDraggable(win);
        });


    }

    function makeWindowResizableAndDraggable(myWindow: Element) {

        const titleBar = myWindow.querySelector('.title-bar');
        if (!titleBar) {
            return;
        }
        let offsetX = 0, offsetY = 0, isDragging = false;

        titleBar.addEventListener('mousedown', (e: any) => {
            isDragging = true;
            offsetX = e.clientX - myWindow.offsetLeft;
            offsetY = e.clientY - myWindow.offsetTop;
            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        });

        function onMouseMove(e) {
            if (!isDragging) return;
            myWindow.style.left = `${e.clientX - offsetX}px`;
            myWindow.style.top = `${e.clientY - offsetY}px`;
        }

        function onMouseUp() {
            isDragging = false;
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
            setWindowDimensionsAction(myWindow.id, getWindowDimensionsAndLocation(myWindow));
        }

        // Resizable functionality scoped to each window
        const resizeHandles = myWindow.querySelectorAll('.resize-handle');

        resizeHandles.forEach(handle => {
            handle.addEventListener('mousedown', (e) => resizeMouseDown(e, myWindow));
        });

        function resizeMouseDown(e, myWindow) {
            const currentHandle = e.target;
            const startX = e.clientX;
            const startY = e.clientY;
            const startWidth = parseInt(document.defaultView.getComputedStyle(myWindow).width, 10);
            const startHeight = parseInt(document.defaultView.getComputedStyle(myWindow).height, 10);

            function resizeMouseMove(e) {
                let newWidth = startWidth + e.clientX - startX;
                let newHeight = startHeight + e.clientY - startY;

                if (currentHandle.classList.contains('resize-handle-se')) {
                    myWindow.style.width = `${newWidth}px`;
                    myWindow.style.height = `${newHeight}px`;
                } else if (currentHandle.classList.contains('resize-handle-sw')) {
                    myWindow.style.width = `${newWidth}px`;
                    myWindow.style.height = `${newHeight}px`;
                    myWindow.style.left = `${e.clientX}px`;
                }
            }

            function resizeMouseUp() {
                setWindowDimensionsAction(myWindow.id, getWindowDimensionsAndLocation(myWindow));
                document.removeEventListener('mousemove', resizeMouseMove);
                document.removeEventListener('mouseup', resizeMouseUp);
            }

            document.addEventListener('mousemove', resizeMouseMove);
            document.addEventListener('mouseup', resizeMouseUp);
        }
    }


    function minimizeWindow() {
        if (!currentWindow) {
            return;
        }
        // Implement minimize functionality
        // currentWindow.style.display = 'none';
        setMinimizedWindowAction(windowInstance.id, true);
    }

    function maximizeWindow() {
        if (!currentWindow) {
            console.log('Current Window not found')
            return;
        }
        // set the window dimensions to the viewport dimensions
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        const dimensions = getWindowDimensionsAndLocation(currentWindow);

        if (windowInstance.maximized) {
            setMaximizedWindowAction(windowInstance.id, false);
            setWindowDimensionsAndLocation(currentWindow, {width: 400, height: 500, left: 0, top: 0});
        } else {
            setMaximizedWindowAction(windowInstance.id, true);
            setWindowDimensionsAndLocation(currentWindow, {
                width: viewportWidth,
                height: viewportHeight - 100,
                left: 0,
                top: 50
            });
        }

        setWindowDimensionsAction(currentWindow.id, dimensions);
    }

    function closeWindow() {
        // Implement close functionality
        setClosedWindowAction(windowInstance.id);
    }

    function getWindowDimensionsAndLocation(myWindow: HTMLElement) {
        const dimensions = {
            width: myWindow.offsetWidth,
            height: myWindow.offsetHeight,
            left: myWindow.offsetLeft,
            top: myWindow.offsetTop
        };
        return dimensions;
    }

    function setWindowDimensionsAndLocation(myWindow: HTMLElement, dimensions: { width: number, height: number, left: number, top: number }) {
        myWindow.style.width = `${dimensions.width}px`;
        myWindow.style.height = `${dimensions.height}px`;
        myWindow.style.left = `${dimensions.left}px`;
        myWindow.style.top = `${dimensions.top}px`;
    }
</script>
<div id={windowInstance.id}
     class:hidden={windowInstance.minimized}
     class="window">
    <div class="title-bar">
        <div class="title-bar-text">{@html windowInstance.title} | {windowInstance.id}</div>
        <div class="title-bar-controls">
            <button aria-label="Minimize" on:click={minimizeWindow} title="Minimize">
                <svg class="w-6 h-6"
                        xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><path fill="currentColor" d="M20 14H4v-4h16"/></svg>
            </button>
            <button aria-label="Maximize" on:click={maximizeWindow} title="Maximize">
                <svg class="w-6 h-6"
                        xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><path fill="currentColor" d="M4 4h16v16H4zm2 4v10h12V8z"/></svg>
            </button>
            <button aria-label="Close" on:click={closeWindow} title="Close Window">
                <svg class="w-6 h-6"
                        xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><path fill="currentColor" d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z"/></svg>
            </button>
        </div>
    </div>
    <div class="content h-full w-full">
        {#if type === 'iframe'}
            <iframe class="h-full w-full" src={windowInstance.editUrl()}></iframe>
        {/if}
        <slot></slot>
    </div>
<!--    <div class="resize-handle resize-handle-sw"></div>-->
    <div class="resize-handle resize-handle-se"></div>
</div>
{#if windowInstance.active}

    {/if}
<style>
    .window {
        position: fixed;
        width: 400px;
        height: 500px;
        background: #f0f0f0;
        border: 1px solid #000;
        border-radius: 5px;
        overflow: hidden;
        z-index: 99998;
    }

    .title-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #2e8b57;
        color: #fff;
        cursor: move;
        padding: 5px 10px;
    }

    .title-bar-text {
        flex-grow: 1;
    }

    .title-bar-controls button {
        background: none;
        border: none;
        color: #fff;
        cursor: pointer;
    }

    .content {
        padding: 10px;
    }

    .resize-handle {
        width: 10px;
        height: 10px;
        position: absolute;
        background: #666;
        z-index: 10;
    }

    .resize-handle-sw {
        left: 0;
        bottom: 0;
        cursor: sw-resize;
    }

    .resize-handle-se {
        right: 0;
        bottom: 0;
        cursor: se-resize;
    }
</style>
