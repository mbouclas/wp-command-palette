<script lang="ts">
    import type {ICommand} from "../models/commands";
    import {modalStore, setSelectedCommandAction, setSelectedItemAction} from "../stores/modal.store";
    export let query: string = '';
    let allCommands: ICommand[] = [
        {
            name: 'Command 1',
            description: 'Description for Command 1'
        },
        {
            name: 'Command 2',
            description: 'Description for Command 2'
        },
        {
            name: 'Command 3',
            description: 'Description for Command 3'
        }
    ];
    let commands: ICommand[] = [];
    let selectedCommandIndex = 0;

    modalStore.subscribe(state => {
        if (query.charAt(0) !== '/') {
            return;
        }

        query = state.query.slice(1);
        commands = [...allCommands].filter(command => command.name.toLowerCase().includes(query.toLowerCase()));
    });


    function markFilteredCommands(command: ICommand) {
        // underline the matched characters on the command name based on query
        const name = command.name;
        const queryLength = query.length;
        const nameLength = name.length;
        const start = name.toLowerCase().indexOf(query.toLowerCase());
        const end = start + queryLength;
        const firstPart = name.slice(0, start);
        const middlePart = name.slice(start, end);
        const lastPart = name.slice(end, nameLength);


        return `${firstPart}<u class="underline bg-red-600 text-white">${middlePart}</u>${lastPart}`;
    }

    function onKeyPressed(e: KeyboardEvent) {
        // handle up arrow
        if (e.key === 'ArrowUp') {
            onUpArrow();
            return;
        }

        // handle down arrow
        if (e.key === 'ArrowDown') {
            onDownArrow();
            return;
        }

        // handle enter key
        if (e.key === 'Enter') {
            onEnter();
            return;
        }


    }

    function onUpArrow() {
        // apply the selected class to the previous command
        if (selectedCommandIndex === 0) {
            return;
        }

        selectedCommandIndex--;
        console.log('up arrow')
    }

    function onDownArrow() {
        // apply the selected class to the next command
        if (selectedCommandIndex === commands.length - 1) {
            return;
        }

        selectedCommandIndex++;
        console.log('down arrow')
    }

    function onEnter() {
        // run the selected command
        if (commands.length === 0) {
            return;
        }

        if (selectedCommandIndex === -1 || selectedCommandIndex >= commands.length || !commands[selectedCommandIndex]) {
            return;
        }


        setSelectedCommandAction(commands[selectedCommandIndex]);
        setSelectedItemAction({
            type: 'command',
            label: commands[selectedCommandIndex].name,
        })

        console.log('enter', commands[selectedCommandIndex])
    }


</script>
<svelte:window on:keydown={onKeyPressed}/>
<div class="flex flex-col gap-4">
    {#each commands as command, index}
        <div class:bg-gray-200={selectedCommandIndex === index}
                class="flex justify-between gap-2 hover:bg-gray-200 hover:cursor-pointer">
            <div class="">
                <h2 class="text-lg font-bold">{@html markFilteredCommands(command)}</h2>
                <p>{command.description}</p>
            </div>
            <span></span>
            <div class="">
                <button class="text-sm text-white bg-indigo-500 p-2" on:click={onEnter}>Run</button>
                <button class="text-sm text-white bg-indigo-500 p-2" on:click={onEnter}>Options</button>

            </div>
        </div>
    {/each}
</div>
