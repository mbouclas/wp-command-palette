import {writable} from "svelte/store";
import type {ICommand} from "../models/commands";
export interface ICommandPaletteItem {
    label: string;
    type: 'command' | 'category' | 'link';
}
export interface IModalStore {
    query: string;
    selectedCommand: ICommand | null;
    selectedItem: ICommandPaletteItem | null;
}

export const modalStore = writable<IModalStore>({
    query: '',
    selectedCommand: null,
    selectedItem: null
});

export function updateModalStoreAction(data: Partial<IModalStore>) {
    modalStore.update((state) => {
        state = { ...state, ...data };
        return state;
    });
}

export function updateQueryAction(query: string) {
    updateModalStoreAction({ query });
}

export function setSelectedCommandAction(command: ICommand | null) {
    updateModalStoreAction({ selectedCommand: command });
}

export function setSelectedItemAction(item: ICommandPaletteItem | null) {
    updateModalStoreAction({ selectedItem: item });
}
