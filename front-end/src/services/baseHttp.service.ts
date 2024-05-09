import {appStore, setHttpLoadingAction} from "../stores/app.store";
import {get} from "svelte/store";

export class BaseHttpService {
    baseUrl: string;

    constructor() {
        this.baseUrl = `${get(appStore).baseUrl}/wp-json/mcms-cp/v1/`;
    }


    async get(url: string, params: any = {}) {
        setHttpLoadingAction(true);
        const response = await fetch(`${this.baseUrl}${url}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
            mode: 'cors',
            credentials: "include",
            ...params
        });
        setHttpLoadingAction(false);
        return await response.json();
    }

    async post(url: string, data: any = {}, params: any = {}) {
        setHttpLoadingAction(true);
        const response = await fetch(`${this.baseUrl}${url}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            mode: 'cors',
            credentials: "include",
            body: JSON.stringify(data),
            ...params
        });
        setHttpLoadingAction(false);
        return await response.json();
    }
}
