import {get, writable} from "svelte/store";

export interface IWindowDimensions {
    width: number;
    height: number;
    left: number;
    top: number;
}

export class TaskBarWindow {
    public id = '';
    public title = '';
    public icon = '';
    public component = '';
    public data = {};
    public active = false;
    public minimized = false;
    public maximized = false;
    public closed = false;
    public dimensions: IWindowDimensions = {
        width: 600,
        height: 600,
        left: 0,
        top: 0
    };

    constructor(data: Partial<TaskBarWindow>) {
        Object.assign(this, data);
        if (!this.id) {
            // generate a 6 character guid
            this.id = TaskBarWindow.generateGuid();
        }
    }

    validate() {
        return true;

    }

    editUrl() {
        return `/wp-admin/post.php?post=${this.id}&action=edit`;
    }

    static generateGuid() {
        return Math.random().toString(36).substring(2, 8);
    }
}

export interface ITaskbarStore {
    windows: TaskBarWindow[];
    id: string|null;
}

export const taskbarStore = writable<ITaskbarStore>({
    windows: [],
    id: null,
});

export function openWindowAction(window: Partial<TaskBarWindow>) {
    const instance = new TaskBarWindow(window);
    if (!instance.validate()) {
        throw new Error('Window is invalid failed');
    }
    const state = get(taskbarStore);
    const found = state.windows.find(w => w.id === instance.id);
    if (found) {
        return;
    }

    addWindowAction(instance);
}

export function addWindowAction(window: TaskBarWindow) {
    taskbarStore.update((state) => {
        const found = state.windows.find(w => w.id === window.id);
        if (found) {
            return state;
        }

        window.active = true;
        state.windows.push(window);

        updateLocalStorage(state);
        return state;
    });
}

export function setMinimizedWindowAction(id: string, minimized: boolean) {
    taskbarStore.update((state) => {
        state.windows = state.windows.map(w => {
            if (w.id === id) {
                w.minimized = minimized;
            }
            return w;
        });

        updateLocalStorage(state);
        return state;
    });
}

export function setMaximizedWindowAction(id: string, maximized: boolean) {
    taskbarStore.update((state) => {
        state.windows = state.windows.map(w => {
            if (w.id === id) {
                w.maximized = maximized;
            }
            return w;
        });

        updateLocalStorage(state);
        return state;
    });
}

export function setClosedWindowAction(id: string) {
    taskbarStore.update((state) => {
        state.windows = state.windows.filter(w => w.id !== id);

        updateLocalStorage(state);
        return state;
    });
}

export function setWindowDataAction(id: string, data: any) {
    taskbarStore.update((state) => {
        state.windows = state.windows.map(w => {
            if (w.id === id) {
                w.data = data;
            }
            return w;
        });

        updateLocalStorage(state);
        return state;
    });
}

export function setWindowDimensionsAction(id: string, dimensions: IWindowDimensions) {
    taskbarStore.update((state) => {
        state.windows = state.windows.map(w => {
            if (w.id === id) {
                w.dimensions = dimensions;
            }
            return w;
        });

        updateLocalStorage(state);
        return state;
    });
}


function updateLocalStorage(state: ITaskbarStore) {
    localStorage.setItem('taskbarStore', JSON.stringify(state));
}

export function restoreTaskBarFromLocalStorageAction() {
    const state = get(taskbarStore);

    const cachedState = JSON.parse(localStorage.getItem('taskbarStore') || '{}');

    if (state.id !== cachedState.id) {
        return;
    }

    if (!Array.isArray(cachedState.windows)) {
        return;
    }

    const windows = cachedState.windows.map((w: object) => new TaskBarWindow(w));
    taskbarStore.update((state) => {
        state.windows = windows;
        return state;

    });

}

export function windowExists(id: string) {
    const state = get(taskbarStore);
    return state.windows.find(w => w.id === id);
}


export function setTaskBarIdAction(id: string) {
    taskbarStore.update((state) => {
        state.id = id;

        updateLocalStorage(state);
        return state;
    });
}


export function initTaskBarAction() {
    // running in iframe, ignore
    if (window.self !== window.top) {
        return;
    }

    const state = get(taskbarStore);
    if (state.id) {
        return;
    }
    const cachedState = JSON.parse(localStorage.getItem('taskbarStore') || '{}');

    const id = (cachedState.id || TaskBarWindow.generateGuid());

    taskbarStore.update((state) => {
        state.id = id;
        state.windows = (Array.isArray(cachedState.windows)) ? cachedState.windows.map((w: object) => {
            const window = new TaskBarWindow(w);
            if (window.maximized) {window.maximized = false;}
            window.minimized = true;
            return window;
        }) : [];
        return state;
    });
}
