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
    <section class="flex flex-col grow-1 justify-end border-y overflow-y-auto">
        {{-- <article class="message">Fake Message 1</article> --}}
        {{-- <article class="message">Fake Message 2</article> --}}
        {{-- <article class="message">Fake Message 3</article> --}}
        <template x-for="item in list" :key="item.message_id">
            <article x-data="chatMessage(item)" class="message" :style="{'--chatter-color': item.color}">
                <header class="">
                    <span class="timestamp" x-text="messageTime"></span>
                    <span class="username" x-text="item.chatter_user_name"></span><span x-text="isMeCommand ? ' ' : ':'"></span>
                </header>
                <p :class="isMeCommand ? 'me-command' : ''">
                    <template x-for="word in words">
                        <span x-html="word.html"></span>
                    </template>
                </p>
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
        display: flex;
        align-items: center;

        header {
            padding-right: .25em;
        }

        .emote {
            display: inline;
            height: 1.5rem;
            vertical-align: text-bottom;
        }

        .me-command {
            font-style: italic;
            color: var(--chatter-color);
        }

        .timestamp {
            font-family: monospace;
        }

        .username {
            color: var(--chatter-color);
            font-weight: bold;
        }
    }
</style>
<style global>
    .fi-page,
    .fi-page>.fi-page-header-main-ctn,
    .fi-page>.fi-page-header-main-ctn>.fi-page-main,
    .fi-page>.fi-page-header-main-ctn>.fi-page-main>.fi-page-content  {
        height: 100%;
        max-height: calc(100vh - 4rem);
    }
</style>
