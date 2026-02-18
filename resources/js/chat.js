import TwitchEmoticons from '@mkody/twitch-emoticons'
const { EmoteFetcher, EmoteParser } = TwitchEmoticons

document.addEventListener('alpine:init', async () => {
    async function initFetcher() {
        const secrets = await (await fetch('/twitch-secrets')).json()

        const channelId = secrets.channelId;

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

        return Promise.resolve(parser)
    }

    Alpine.data('chatPanel', function () {
        return {
            list: this.$persist([]),
            parser: null,
            init() {
                Native.on('App\\Events\\NewChatMessage', (payload) => {
                    // console.log('payload', payload.event)
                    this.list.push(payload.event)
                })
                setTimeout(() => initFetcher().then((parser) => console.log(this.parser = parser)), 0)
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
        twitchEvent.message.fragments.forEach(fragment => fragment.html = fragment.text)
        return {
            messageTime: formatTime(twitchEvent.created_at ?? null),
            isMeCommand: twitchEvent.message.text.startsWith('\u0001ACTION'),
            words: twitchEvent.message.fragments,
            init() {
                if (this.parser) {
                    this.words.forEach(word => {
                        word.html = this.parser.parse(word.text)
                    })
                } else {
                    this.$watch('parser', () => {
                        this.words.forEach(word => {
                            word.html = this.parser.parse(word.text)
                        })
                    })
                }
            },
        }
    })
})
