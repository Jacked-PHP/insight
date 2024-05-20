
<div class="h-[calc(100vh-80px)] flex flex-col p-4 mx-auto max-w-4xl bg-white" x-data="ollamaclient">
    <ul role="list" class="flex-grow space-y-6 pt-[10px] pr-[5px] overflow-auto" x-ref="chatcontainer">
        <template x-for="message in messages">
            <li class="relative flex gap-x-4">
                <div class="absolute left-0 top-0 flex w-6 justify-center -bottom-6">
                    <div class="w-px bg-gray-200"></div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="relative mt-3 h-6 w-6 flex-none rounded-full bg-gray-50">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>

                <div class="flex-auto rounded-md p-3 ring-1 ring-inset ring-gray-200">
                    <div class="flex justify-between gap-x-4">
                        <div class="py-0.5 text-xs leading-5 text-gray-500">
                            <span class="font-medium text-gray-900" x-text="message.sender.name"></span>
                        </div>
                        <time datetime="2023-01-23T15:56" class="flex-none py-0.5 text-xs leading-5 text-gray-500" x-text="message.timestamp"></time>
                    </div>
                    <p class="text-md leading-6 text-gray-800" x-html="message.message"></p>
                </div>
            </li>
        </template>
    </ul>

    <!-- Input -->
    <div class="mt-6 flex gap-x-3 flex-grow-0">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 flex-none rounded-full bg-gray-50">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>

        <div action="#" class="relative flex-auto">
            <div class="overflow-hidden rounded-lg pb-12 shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-indigo-600">
                <label for="comment" class="sr-only">Add your comment</label>
                <textarea @keyup.enter="sendMessage()" rows="1" name="comment" x-ref="message"
                          class="block w-full resize-none border-0 bg-transparent py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"
                          placeholder="Add your message..."></textarea>
            </div>

            <div class="absolute inset-x-0 bottom-0 flex justify-between py-2 pl-3 pr-2">
                <div class="flex items-center space-x-5" @click.outside="showMessageMenu && toggleMessageMenu()">
                    <div class="flex items-center">
                        <button type="button" class="-m-2.5 flex h-10 w-10 items-center justify-center rounded-full text-gray-400 hover:text-gray-500" @click="showMessageMenu && toggleMessageMenu()">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M15.621 4.379a3 3 0 00-4.242 0l-7 7a3 3 0 004.241 4.243h.001l.497-.5a.75.75 0 011.064 1.057l-.498.501-.002.002a4.5 4.5 0 01-6.364-6.364l7-7a4.5 4.5 0 016.368 6.36l-3.455 3.553A2.625 2.625 0 119.52 9.52l3.45-3.451a.75.75 0 111.061 1.06l-3.45 3.451a1.125 1.125 0 001.587 1.595l3.454-3.553a3 3 0 000-4.242z"
                                      clip-rule="evenodd"/>
                            </svg>
                            <span class="sr-only">Attach a file</span>
                        </button>
                    </div>
                </div>
                <button @click="sendMessage()" type="button" class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Send <span class="text-gray-400 text-xs">[Enter]</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    const eventTemplate = {
        userUuid: null,
        messageUuid: null,
        message: null,
        timestamp: null,
    }

    const participantTemplate = {
        type: null, // 'user' | 'bot'
        uuid: null,
        name: null,
        avatar: null,
    }

    const messageTemplate = {
        sender: {
            ...participantTemplate,
        },
        uuid: null,
        message: null,
        rawMessage: null,
        timestamp: null,
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('ollamaclient', () => ({
            showMessageMenu: false,
            chatAvailable: false,
            conveyor: null,
            userId: null,

            /**
             * @type {Array.<participantTemplate>}
             */
            participants: @entangle('participants'),

            /**
             * @type {Object.<messageTemplate>}
             */
            messages: [],

            init() {
                this.messages = this.$wire.messages.map((message) => {
                    message.message = window.marked(message.message)
                    return message
                })
                this.userId = this.$wire.userId
                this.startWsConnection()

                document.addEventListener('alpine:initialized', () => {
                    this.keepScrollDown(true)
                    this.$refs.message.focus()
                })
            },

            startWsConnection() {
                const connect = (token) => {
                    this.conveyor = new window.Conveyor({
                        protocol: '{{ $protocol }}',
                        uri: '{{ $host }}',
                        port: {{ $port }},
                        channel: '{{ $channel }}',
                        query: '?token=' + token,

                        /**
                         * @param {eventTemplate} e
                         */
                        onMessage: (e) => {
                            const isScrollAtBottom = this.isScrollAtBottom()

                            const parsedData = JSON.parse(e);
                            const participantIndex = this.participants
                                .findIndex(p => p.uuid === (parsedData.userId ?? 'bot-alfred'))
                            const messageParticipant = {
                                ...participantTemplate,
                                ...this.participants[participantIndex],
                            }

                            const messageIndex = this.messages
                                .findIndex(m => m.uuid === parsedData.messageUuid)
                            if (messageIndex === -1) {
                                this.addMessage(
                                    messageParticipant,
                                    parsedData.messageUuid,
                                    parsedData.message,
                                    parsedData.message,
                                    parsedData.timestamp,
                                )
                                return
                            }

                            this.messages[messageIndex].message += parsedData.message
                            this.messages[messageIndex].rawMessage += parsedData.message

                            this.messages[messageIndex].message = window.marked(this.messages[messageIndex].rawMessage)

                            this.keepScrollDown(isScrollAtBottom)
                        },
                        onReady: () => {
                            this.chatAvailable = true
                        },
                    })
                }

                ((callback) => {
                    fetch('/broadcasting/auth?channel_name={{ $channel }}', {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    })
                        .then(response => response.json())
                        .then(data => callback(data.auth))
                        .catch(error => console.error(error))
                })(connect)
            },

            addMessage(
                participant,
                messageUuid,
                message,
                rawMessage,
                timestamp,
            ) {
                this.messages.push({
                    ...messageTemplate,
                    sender: participant,
                    uuid: messageUuid,
                    message: message,
                    rawMessage: rawMessage,
                    timestamp: timestamp,
                })
            },

            toggleMessageMenu() {
                this.showMessageMenu = !this.showMessageMenu
            },

            sendMessage() {
                const message = this.$refs.message.value
                const timestamp = new Date().toISOString()
                const participantIndex = this.participants
                    .findIndex(p => p.uuid === this.userId)
                const messageParticipant = {
                    ...participantTemplate,
                    ...this.participants[participantIndex],
                }

                this.addMessage(
                    messageParticipant,
                    null,
                    message,
                    message,
                    timestamp,
                )

                this.$wire.postMessage(message)
                this.$refs.message.value = ''

                setTimeout(() => this.keepScrollDown(true), 100);
            },

            isScrollAtBottom() {
                const container = this.$refs.chatcontainer
                return (container.scrollHeight - container.scrollTop - container.clientHeight) <= 9000
            },

            keepScrollDown(keep) {
                if (keep) {
                    const container = this.$refs.chatcontainer
                    container.scrollTop = container.scrollHeight
                }
            },
        }))
    })
</script>
