<script lang="ts">
    import {openWindowAction} from "../stores/taskbar.store";

    export let items: any[] = [];
    if (!Array.isArray(items)) {
        items = [];
    }

    function openInWindow(item: any) {
        openWindowAction({
            title: item.post_title,
            data: item,
            id: (typeof item.ID === 'number' ? item.ID.toString() : item.ID),
        })
    }
</script>
<div class="flex flex-col gap-4">
    {#if Array.isArray(items)}
    {#each items as item}
        <div class="flex justify-between gap-2">
            <div>
                <h2 class="text-lg font-bold">{item.post_title}</h2>
                <p>#{item.ID} | {item.post_date}</p>
            </div>
            <span></span>
            <div class="">
                <button>Add to favorites</button>
                <button>Open in new tab</button>
                <button on:click={openInWindow.bind(null, item)}>Open in window</button>
            </div>
        </div>
    {/each}
        {:else}
        <p>No items found</p>
        {/if}
</div>
