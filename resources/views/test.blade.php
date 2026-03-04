<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss-browser/4.1.13/index.global.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.15.0/cdn.js"></script>
    <style type="text/tailwindcss">
        @theme {
            --ease-bounce-in: cubic-bezier(0.34, 1.56, 0.64, 1);
            --ease-bounce-out: cubic-bezier(0.36, 0, 0.66, -0.56);
        }
        img {
            display: inline-block;
            height: 1.5em;
            width: auto;
        }

        .h-custom {
            height: var(--custom-height);
        }

        .right-custom {
            right: var(--custom-right);
        }

        .my-transition-in {
            transition: opacity 1.1s ease-out, height .3s linear, right .8s var(--ease-bounce-in) .3s;
        }
        .my-transition-out {
            transition: opacity .5s ease-in .3s, scale .8s var(--ease-bounce-out);
        }
    </style>
</head>
<body class="h-screen">
<div x-data="blocks" class="absolute top-1/3 h-[300px] left-8 flex flex-col justify-end">
    <template x-for="item in messages" :key="item.id">
        <div
            x-data="block(item.message)"
            x-show="show"
            class="shrink-0"
            :class="{
                '': show,
            }"
            x-transition:enter="my-transition-in overflow-hidden relative duration-2000"
            x-transition:enter-start="opacity-0 h-4 right-custom"
            x-transition:enter-end="opacity-100 h-custom right-0"
            x-transition:leave="my-transition-out overflow-hidden relative duration-2000"
            x-transition:leave-start=""
            x-transition:leave-end="opacity-0 scale-0"
        >
            <div class="mt-2 w-[500px] bg-orange-800/50 p-4 rounded-lg" x-ref="content"></div>
        </div>
    </template>
</div>

<script>
    function areAllImagesLoaded(container) {
        const images = Array.from(container.querySelectorAll('img'))
        if (images.length === 0) return Promise.resolve(true)

        const promises = images
            .filter((img) => !img.complete) // Filter out already completed images
            .map(
                (img) =>
                    new Promise((resolve) => {
                        img.addEventListener('load', () => resolve(true), { once: true })
                        img.addEventListener('error', () => resolve(false), { once: true }) // Treat error as completion of loading attempt
                    }),
            );

        return Promise.all(promises).then(() => {
            // console.log('All images (or attempts) are complete.');
            // Check naturalWidth here if you need to know if they succeeded
            return images.every((img) => img.complete && img.naturalWidth > 0);
        });
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('blocks', () => {
            return {
                messages: [],
                init() {
                    window.addEventListener('frag-added', (evt) => this.messages.push(evt.detail))
                },
            }
        })
        Alpine.data('block', (content) => {
            return {
                content,
                show: false,
                init() {
                    this.$refs.content.innerHTML = content;
                    areAllImagesLoaded(this.$el).then(() => this.start());
                    this.$watch('show', (show) => {
                        if (! show) {
                            setTimeout(() => this.messages.shift(), 1500)
                        }
                    })
                },
                start() {
                    this.$el.style.setProperty('display', 'block')
                    const box = this.$el.getBoundingClientRect()
                    this.$el.style.setProperty('--custom-right', box.width + box.x + 'px')
                    this.$el.style.setProperty('--custom-height', box.height + 'px')
                    this.show = true
                    setTimeout(() => this.show = false, 30000)
                }
            }
        })
    })

    document.addEventListener('alpine:initialized', () => {
        const fragment = `
            Something <img src="https://placecats.com/20/20"> else <img src="https://placecats.com/30/20">
            Something <img src="https://placecats.com/21/20"> else <img src="https://placecats.com/31/20">
            Something <img src="https://placecats.com/22/20"> else <img src="https://placecats.com/32/20">
            Something <img src="https://placecats.com/23/20"> else <img src="https://placecats.com/33/20">
        `

        function send (name) {
            const content = fragment.replaceAll('.com', '.com/'+name)
            window.dispatchEvent(new CustomEvent('frag-added', { detail: { id: name, message: content } }))
        }
        send('millie')
        setTimeout(() => send('neo'), 4000)
        setTimeout(() => send('poppy'), 4500)
        setTimeout(() => send('neo_2'), 6000)
        setTimeout(() => send('bella'), 8500)
        setTimeout(() => send('louie'), 9500)
    })
</script>

</body>
</html>
