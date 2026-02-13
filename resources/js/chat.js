document.addEventListener('alpine:init', () => {
    Alpine.data('chatPanel', () => {
        return {
            list: [],
            init() {
                Native.on('App\\Events\\NewChatMessage', (payload) => {
                    // console.log('payload', payload.event)
                    this.list.push(payload.event)
                })
            }
        };
    })
})
