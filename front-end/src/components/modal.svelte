<script lang="ts">
import {appStore, setSelectPostTypeAction} from "../stores/app.store";
import {QueryService} from "../services/query.service";
import Skeleton from "./skeleton.svelte";
import ResultList from "./result-list.svelte";
import CommandsList from "./commands-list.svelte";
import Chip from "./chip.svelte";
import {modalStore, setSelectedCommandAction, setSelectedItemAction, updateQueryAction} from "../stores/modal.store";

let previousState = false;
let query = '';
let timer: number;
let results: any = {};
let showCommandList = false;


appStore.subscribe(async state => {
    if (!state || !state.modalOpen) {return;}
    if (state.modalOpen !== previousState) {
        previousState = state.modalOpen;
        const el = document.querySelector('command-palette');
        if (!el) {
            return;
        }
        const shadow = el.shadowRoot;

        if (!shadow) {
            return;
        }

        setTimeout(() => {
            const input = shadow.querySelector('input');
            if (input) {
                input.focus();
            }
        }, 100);
        await search();
    }
    // console.log(state)

/*    if (state.modalOpen) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = 'auto';
    }*/

});



function reset() {
    query = '';
    results = [];
}

function onInput(e: InputEvent) {
    if (e.target && e.target.value.length === 0) {
        reset();
    }
}

async function onInputChanged() {
    clearTimeout(timer);
    updateQueryAction(query);
    // if the character equals to / then it's a command
    if (query.charAt(0) === '/') {
        // run command
        showCommands();
        return;
    }

    timer = setTimeout(async () => {
        if (query.length === 0) {return;}

        await search();

    }, 250);

}

async function search() {
    const res = await (new QueryService).simpleSearch(query, $appStore.selectedPostType);
    results = res['posts'];

}

async function onPostTypeSelected(slug: string) {
    setSelectPostTypeAction(slug);
    await search();
}

function showCommands() {
    results = {};
    showCommandList = true;
}

function onCommandDismiss() {
    setSelectedCommandAction(null);
    setSelectedItemAction(null);
    showCommandList = false;
    reset();
}

</script>


{#if $appStore.modalOpen}
    <div
            class="relative z-10" role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity"></div>

        <div class="fixed inset-0 z-[99999] w-screen overflow-y-auto p-4 sm:p-6 md:p-20">

            <div class="mx-auto max-w-4xl max-h-[600px] overflow-y-auto transform overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all">
                <div
                        class="grid-cols-12 grid p-2 flex gap-1.5">
                    <div id="commands" class={`${$modalStore.selectedItem ? 'col-span-3' : ''}`}>
                        {#if ($modalStore && $modalStore.selectedItem)}
                            <Chip onDismiss={onCommandDismiss}>{$modalStore.selectedItem.label}</Chip>

                            {/if}
                    </div>

                    <input id="cp-search-input" type="search" bind:value={query}
                           on:keyup={onInputChanged} on:input={onInput}
                           class:col-span-9={($modalStore && $modalStore.selectedItem)}
                           class:col-span-12={(!$modalStore || !$modalStore.selectedItem)}
                           class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                           placeholder="Search or run command" role="combobox" aria-expanded="false" aria-controls="options">
                </div>

                <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200">

                    <ul class="flex flex-wrap -mb-px">
                        <li class="me-2">
                            <button on:click={onPostTypeSelected.bind(null, 'all')}
                                    class={`${$appStore.selectedPostType === 'all' ? 'text-blue-600 border-blue-600 active' : 'border-transparent'} border-b-2 inline-block p-4 rounded-t-lg`} aria-current="page">
                                All
                            </button>
                        </li>
                        {#each $appStore.allowedPostTypes as postType}
                            <li class="me-2">
                                <button on:click={onPostTypeSelected.bind(null, postType.slug)}
                                        class={`${$appStore.selectedPostType === postType.slug ? 'text-blue-600 border-blue-600 active' : 'border-transparent'} border-b-2 inline-block p-4 rounded-t-lg`}
                                >
                                    {postType.name}
                                </button>
                            </li>
                        {/each}



                    </ul>

                </div><!-- END TOP BAR -->
                <div class="grid grid-cols-12 gap-4">
                <div class="px-4 col-span-8">
                    {#if $appStore.httpLoading}
                        <div class="my-8 flex  justify-center">
                            <Skeleton />

                        </div>
                    {/if}
                <div id="ResultsArea">
                    Results are shown here conditionally.
                    It could be search results, commands, actions or favorites
                </div>
                    <div class="border-t border-gray-100  py-14  text-sm ">
                        {#if showCommandList}
                            <CommandsList {query} />
                        {/if}
                        {#if Object.keys(results).length > 0 && $appStore.selectedPostType === 'all'}
                            {#each Object.keys(results) as key}
                                <div class="my-4">
                                    <h4 class="text-2xl bg-black p-2 text-white">{key}</h4>
                                    {#if Array.isArray(results[key])}
                                        <ResultList items={results[key]} />
                                        <button on:click={setSelectPostTypeAction.bind(null, key)}>
                                            Filter by this type
                                        </button>
                                    {/if}
                                </div>
                            {/each}
                        {/if}
                        {#if Object.keys(results).length > 0 && $appStore.selectedPostType !== 'all'}
                            <div class="my-4">

                                <ResultList items={results[$appStore.selectedPostType]} />
                            </div>
                        {/if}
                    </div>
                </div><!-- END LEFT -->
                    <div class="px-4 py-14 col-span-4">
                        <h2>Favorites</h2>
                    </div><!-- END RIGHT -->
                </div>
            </div>
        </div>
    </div>
{/if}
