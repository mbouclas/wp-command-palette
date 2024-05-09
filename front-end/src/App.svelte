<script lang="ts">
  import {onMount} from "svelte";
  import {attachStylesToShadowRoot} from "./helpers/shadow-root";
  import {initAppAction, setModalAction, updateAppStoreAction} from "./stores/app.store";
  import Modal from './components/modal.svelte';
  import Taskbar from "./components/taskbar.svelte";
  export let baseUrl = '';

  attachStylesToShadowRoot('command-palette');

  onMount(async () => {
    updateAppStoreAction({baseUrl, modalOpen: true});
    await initAppAction();

  });

  function onKeyPressed(e: KeyboardEvent) {
    if (e.ctrlKey && e.key === 'k') {
      setModalAction(true);
      e.preventDefault();


      return
    }

    if (e.key === 'Escape') {
      setModalAction(false);
    }
  }

  function onRegisterCommand(e: CustomEvent) {
    console.log('register command', e)
  }


</script>

<svelte:options customElement={{
    tag: 'command-palette',
    shadow: 'open'
}}></svelte:options>
<svelte:window
        on:keydown={onKeyPressed}
        on:cp-register-command={onRegisterCommand}
/>

<Modal/>

<Taskbar />
