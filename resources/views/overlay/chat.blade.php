<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chat Overlay</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .my-transition-in {
            transition: opacity 1.1s ease-out, height .3s linear, right .8s var(--ease-bounce-in) .3s;
        }
        .my-transition-out {
            transition: opacity .5s ease-in .3s, scale .8s var(--ease-bounce-out);
        }

        .message {
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
</head>
<body class="bg-black text-white">
<div class="absolute left-8 top-[50%] w-72 h-48" x-data="chatPanel()">
    <section class="flex flex-col gap-4 justify-end h-full">
        <template x-for="item in localList" :key="item.message_id">
            <article
                x-data="chatMessage(item)"
                class="shrink-0 message rounded-lg bg-orange-900/70 inset-shadow-red-500 inset-shadow-sm"
                :style="{'--chatter-color': item.color, 'opacity': show ? 1 : 0.1}"
                x-show="show"
                x-transition:enter="my-transition-in overflow-hidden relative duration-2000"
                x-transition:enter-start="opacity-0 h-4 right-custom"
                x-transition:enter-end="opacity-100 h-custom right-0"
                x-transition:leave="my-transition-out overflow-hidden relative duration-2000"
                x-transition:leave-start=""
                x-transition:leave-end="opacity-0 scale-0"
            >
                <div class="m-4">
                    <header class="">
                        <span class="username" x-text="item.chatter_user_name"></span><span class="username" x-text="isMeCommand ? ' ' : ':'"></span>
                    </header>
                    <p :class="isMeCommand ? 'me-command' : ''">
                        <template x-for="word in words">
                            <span x-html="word.html"></span>
                        </template>
                    </p>
                </div>
            </article>
        </template>
    </section>
</div>
</body>
</html>
