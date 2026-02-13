<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<section x-data="chatPanel()" class="flex flex-col border bg-slate-800 rounded-xl overflow-hidden">
    <header class="flex justify-center items-center">
        <h1 class="text-2xl font-bold">Chat</h1>
    </header>
    <section class="flex flex-col grow-1 justify-end border-y">
        {{-- <article class="message">Fake Message 1</article> --}}
        {{-- <article class="message">Fake Message 2</article> --}}
        {{-- <article class="message">Fake Message 3</article> --}}
        <template x-for="item in list" :key="item.message_id">
            <article class="message">
                <p x-text="item.message.text"></p>
            </article>
        </template>
    </section>
    <footer class="flex justify-center items-center">
        <input type="text" class="w-full p-2 rounded-b-xl" placeholder="Type a message...">
    </footer>
    <template x-ref="chatMessage">
        <article>
            <p data-message="body"></p>
        </article>
    </template>
</section>

<style>
    .message {
        padding: .5rem;
        border-bottom: 1px solid #ccc;
        &:last-of-type {
            border-bottom: 0;
        }
    }
</style>
<style global>
    .fi-page,
    .fi-page>.fi-page-header-main-ctn,
    .fi-page>.fi-page-header-main-ctn>.fi-page-main,
    .fi-page>.fi-page-header-main-ctn>.fi-page-main>.fi-page-content  {
        height: 100%;
    }
</style>
