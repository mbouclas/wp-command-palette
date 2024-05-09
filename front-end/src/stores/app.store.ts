import {writable} from "svelte/store";
import {BaseHttpService} from "../services/baseHttp.service";
export interface IPostType {
    name: string;
    slug: string;
    rest_base: string;
    _links: any;
}
export interface IAppStore {
    baseUrl: string;
    ready: boolean;
    modalOpen: boolean;
    httpLoading: boolean;
    allowedPostTypes: IPostType[];
    allowedTaxonomies: IPostType[];
    selectedPostType: string;
}

export const appStore = writable<IAppStore>({
    baseUrl: '/',
    ready: false,
    modalOpen: false,
    httpLoading: false,
    allowedPostTypes: [],
    allowedTaxonomies: [],
    selectedPostType: 'all'
});

export function updateAppStoreAction(data: Partial<IAppStore>) {
    appStore.update((state) => {
        state = { ...state, ...data };
        localStorage.setItem('appStore', JSON.stringify(state));
        return state;
    });
}

export function toggleModalAction() {
    appStore.update((state) => {
        state.modalOpen = !state.modalOpen;
        return state;
    });
}

export function setModalAction(open: boolean) {
    appStore.update((state) => {
        state.modalOpen = open;
        return state;
    });

}

export async function initAppAction() {
    const res = await (new BaseHttpService()).get('boot');

    updateAppStoreAction({...res,  ready: true });
}

export function setSelectPostTypeAction(postType: string) {
    appStore.update((state) => {
        state.selectedPostType = postType;
        return state;
    });
}

export function setHttpLoadingAction(loading: boolean) {
    appStore.update((state) => {
        state.httpLoading = loading;
        return state;
    });
}
