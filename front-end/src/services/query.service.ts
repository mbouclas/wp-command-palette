import {BaseHttpService} from "./baseHttp.service";

export class QueryService extends BaseHttpService {
    async simpleSearch(query: string, postType = 'all') {
        return this.get(`search?q=${query}&postType=${postType}`);
    }
}
