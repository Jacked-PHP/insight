
<div class="h-[calc(100vh-80px)] flex flex-col p-4 mx-auto max-w-4xl bg-white" x-data="ollamaclient">
    <ul role="list" class="flex-grow space-y-6 pt-[10px] pr-[5px] overflow-auto" x-ref="chatcontainer">
        @foreach($messages as $message)
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
                            <span class="font-medium text-gray-900">{{ \Illuminate\Support\Arr::get($message, 'sender.name', '') }}</span>
                        </div>
                        <time datetime="{{ \Illuminate\Support\Arr::get($message, 'timestamp', '') }}" class="flex-none py-0.5 text-xs leading-5 text-gray-500"></time>
                    </div>
                    <p class="text-md leading-6 text-gray-800 message-item-{{ \Illuminate\Support\Arr::get($message, 'type') }}" id="{{ \Illuminate\Support\Arr::get($message, 'uuid') }}">{{ \Illuminate\Support\Arr::get($message, 'message', '') }}</p>
                </div>
            </li>
        @endforeach
    </ul>

    <!-- Input -->
    <div class="mt-6 flex gap-x-3 flex-grow-0">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 flex-none rounded-full bg-gray-50">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>

        <div action="#" class="relative flex-grow flex">
            <div class="overflow-hidden rounded-lg shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-indigo-600 flex-grow">
                <label for="comment" class="sr-only">Add your comment</label>
                <textarea @keyup.enter="sendMessage()" rows="1" name="comment" x-ref="message"
                          class="block w-full h-full resize-none border-0 py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6 bg-transparent"
                          placeholder="Add your message..." :readonly="!chatAvailable"></textarea>
            </div>

            <div class="inset-x-0 bottom-0 flex flex-grow-0 justify-between py-2 pl-3 pr-2">
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
            userId: null,
            chatUuid: '{{ $chatUuid }}',

            /**
             * @type {Array.<participantTemplate>}
             */
            participants: @entangle('participants'),

            init() {
                this.userId = this.$wire.userId

                document.addEventListener('alpine:initialized', () => {
                    this.keepScrollDown(true)
                    this.chatAvailable = true
                    this.$refs.message.focus()
                })
            },

            toggleMessageMenu() {
                this.showMessageMenu = !this.showMessageMenu
            },

            async sendMessage() {
                this.chatAvailable = false
                const interval = setInterval(() => this.keepScrollDown(true), 100)

                const message = this.$refs.message.value
                this.$refs.message.value = ''
                await this.$wire.postMessage(message)

                const requestMessages = document.getElementsByClassName('message-item-request')
                const requestUuid = requestMessages[requestMessages.length - 1].id
                const responseMessages = document.getElementsByClassName('message-item-response')
                const responseUuid = responseMessages[responseMessages.length - 1].id
                const responseDom = document.getElementById(responseUuid)

                fetch('/api/stream-endpoint/' + this.chatUuid + '/' + requestUuid + '/' + responseUuid)
                    .then(response => {
                        const reader = response.body.getReader()
                        const decoder = new TextDecoder()

                        function read() {
                            reader.read().then(({ done, value }) => {
                                if (done) return
                                const text = decoder.decode(value, { stream: true })
                                responseDom.append(text)
                                read()
                            });
                        }

                        read()
                    })
                    .catch(error => {
                        console.error('Error:', error)
                    })
                    .finally(() => {
                        clearInterval(interval)
                        this.chatAvailable = true
                        this.$refs.message.focus()
                    });
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
