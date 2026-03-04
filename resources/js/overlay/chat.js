import TwitchEmoticons from '@mkody/twitch-emoticons'
const { EmoteFetcher, EmoteParser } = TwitchEmoticons

document.addEventListener('alpine:init', async () => {
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


    async function initFetcher() {
        const secrets = await (await fetch('/twitch-secrets')).json()

        const channelId = parseInt(secrets.channelId)
        const channelFetches = {
            'twitch': Promise.resolve(true),
            0: Promise.resolve(true),
            139075904: Promise.resolve(true),
        }
        channelFetches[channelId] = Promise.resolve(true)

        const fetcher = new EmoteFetcher(secrets.clientId, secrets.clientSecret)
        const parser = new EmoteParser(fetcher, {
            // Custom HTML format
            template: '<img class="emote" alt="{name}" src="{link}">',
            // Match without :colons:
            match: /([^\s]+)+?/g
        })

        // await fetcher.fetchSevenTVEmotes()
        await Promise.allSettled([
            // Twitch global
            fetcher.fetchTwitchEmotes(),
            // Twitch global channel
            fetcher.fetchTwitchEmotes(139075904),
            // Twitch channel
            fetcher.fetchTwitchEmotes(channelId),
            // BTTV global
            fetcher.fetchBTTVEmotes(),
            // BTTV channel
            fetcher.fetchBTTVEmotes(channelId),
            // 7TV global
            fetcher.fetchSevenTVEmotes(),
            // 7TV channel
            // fetcher.fetchSevenTVEmotes(channelId),
            // FFZ global
            fetcher.fetchFFZEmotes(),
            // FFZ channel
            fetcher.fetchFFZEmotes(channelId)
        ])

        return Promise.resolve({
            checkChannels(channelIds) {
                const newChannels = channelIds.reduce(function (carry, channelId) {
                    channelId = channelId === 'twitch' ? 'twitch' : parseInt(channelId)
                    if (channelFetches[channelId] === undefined) {
                        channelFetches[channelId] = fetcher.fetchTwitchEmotes(channelId)
                    }

                    if (! carry.includes(channelId)) carry.push(channelId)

                    return carry;
                }, [])

                return Promise.allSettled(newChannels.map(channelId => channelFetches[channelId]))
            },
            parse(text) {
                return parser.parse(text)
            }
        })
    }

    Alpine.data('chatPanel', function () {
        return {
            list: this.$persist([]),
            localList: [],
            parser: null,
            init() {
                this.localList = this.list.slice(-1)
                Echo.channel(`overlay`)
                    .listen('NewChatMessage', (payload) => {
                        this.list.push(payload.event)
                        this.localList.push(payload.event)
                    })
                setTimeout(() => initFetcher().then((parser) => this.parser = parser), 0)
            },
            render(item) {
                return item.message.fragments.map(i => i.text).join(' ')
            },
        }
    })

    Alpine.data('chatMessage', function (twitchEvent) {
        function formatTime(datetime) {
            if (!datetime) return '00:00'
            const date = new Date(datetime)
            return date.getHours() + ':' + date.getMinutes()
        }

        const emoteChannelIds = twitchEvent.message.fragments
            .filter(fragment => fragment.type === 'emote' && fragment.emote.owner_id)
            .map(fragment => fragment.emote.owner_id)

        twitchEvent.message.fragments.forEach(fragment => fragment.html = '')

        return {
            messageTime: formatTime(twitchEvent.created_at ?? null),
            isMeCommand: twitchEvent.message.text.startsWith('\u0001ACTION'),
            words: twitchEvent.message.fragments,
            show: false,
            renderEmoticons() {
                this.parser.checkChannels(emoteChannelIds).then(() => {
                    this.words.forEach(word => {
                        word.html = this.parser.parse(word.text)
                    })
                    areAllImagesLoaded(this.$el).then(() => this.start())
                })
            },
            init() {
                if (this.parser) {
                    this.renderEmoticons()
                } else {
                    this.$watch('parser', () => {
                        this.renderEmoticons()
                    })
                }
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
