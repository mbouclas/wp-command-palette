<script lang="ts">
    import {
        initTaskBarAction,
        setClosedWindowAction,
        setMinimizedWindowAction,
        taskbarStore,
        TaskBarWindow
    } from "../stores/taskbar.store";
    import Window from "./window.svelte";
    import {appStore} from "../stores/app.store";





    appStore.subscribe(state => {
        if (!state.ready) {return;}
        initTaskBarAction();

    })

    function toggleWindowVisibility(window: TaskBarWindow) {
        setMinimizedWindowAction(window.id, !window.minimized);
    }
</script>

{#if $taskbarStore.windows.length > 0}
<div class="h-10 bg-[#1D2327] fixed bottom-0 left-[160px] w-full z-[99999]">
    <div class="flex gap-4">
        {#each $taskbarStore.windows as window}
            <div class="">

                <Window windowInstance={window} type="iframe">

                </Window>
            </div>
            <div class="flex gap-4 bg-[#2b2e34] px-4 hover:bg-[#3d414a] text-white">
                <button title="Toggle visibility"
                        class="p-2 truncate w-full" on:click={toggleWindowVisibility.bind(null, window)}>{window.title}</button>
                <button title="Close window"
                        on:click={() => setClosedWindowAction(window.id)}>X</button>
            </div>
        {/each}
    </div>
</div>
{/if}
