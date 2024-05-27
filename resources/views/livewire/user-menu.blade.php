<div x-data="usermenu" class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
    <div class="relative flex flex-1 items-center">
        <div @click.away="editingChatName = false" class="relative flex items-center group">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 cursor-pointer hidden group-hover:inline absolute left-0 -translate-x-6" x-cloak>
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
            <input
                    x-ref="chatname"
                    :readonly="!editingChatName"
                    x-model="$store.chatData.chatName"
                    @keydown.enter="editingChatName = false"
                    @blur="editingChatName = false"
                    @input="updateWidth"
                    @focus="updateWidth()"
                    @click="toggleChatNameEditing()"
                    type="text"
                    class="border-none focus:ring-0 p-0 m-0 bg-transparent w-auto min-w-2 underline-offset-4 cursor-pointer"
                    :class="{ 'cursor-text underline': editingChatName }"
                    maxlength="100"
            />
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-400" :class="{ 'hidden': !chatNameSaved }" @chatnamesaved.window="showChatNameSaved()" x-cloak>
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
    </div>

    <div class="flex items-center gap-x-4 lg:gap-x-6">
        {{--Notification--}}
        {{--<button type="button" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500">
            <span class="sr-only">View notifications</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
        </button>--}}

        {{--Separator--}}
        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>

        {{--Profile dropdown--}}
        <div class="relative" @click.away="userMenuShow = false" x-cloak>
            <button @click="toggleUserMenuShow()" type="button" class="-m-1.5 flex items-center p-1.5" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                <span class="sr-only">Open user menu</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 rounded-full bg-gray-50">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span class="hidden lg:flex lg:items-center">
                    <span class="ml-4 text-sm font-semibold leading-6 text-gray-900" aria-hidden="true">{{ auth()->user()->name }}</span>
                    <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </span>
            </button>
            <div
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    x-show="userMenuShow"
                    class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
                    role="menu"
                    aria-orientation="vertical"
                    aria-labelledby="user-menu-button"
                    tabindex="-1"
            >
                {{--Active: "bg-gray-50", Not Active: ""--}}
                {{--<a href="#" class="block px-3 py-1 text-sm leading-6 text-gray-900" role="menuitem" tabindex="-1" id="user-menu-item-0">Your profile</a>--}}
                <a href="{{ route('logout') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900" role="menuitem" tabindex="-1" id="user-menu-item-1">Sign out</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('usermenu', () => ({
            userMenuShow: false,
            editingChatName: false,
            chatNameSaved: false,
            chatNameSaveTimeout: null,

            async init() {
                this.$watch('editingChatName', async value => {
                    if (!value) return
                    await this.$nextTick()
                    this.$refs.chatname.focus()
                })

                this.$watch('$store.chatData.chatName', (value, oldValue) => {
                    if (
                        value === oldValue
                        || value.trim().length === 0
                        || !oldValue
                        || (oldValue && oldValue.trim().length === 0)
                    ) return

                    clearTimeout(this.chatNameSaveTimeout)
                    this.chatNameSaveTimeout = setTimeout(async () => {
                        await this.$wire.updateChatName(this.$store.chatData.chatUuid, value)
                        this.updateWidth()
                        this.$dispatch('chatnamesaved')
                    }, 1500)
                })

                await this.$nextTick()
                this.updateWidth()
                await this.$nextTick()
            },

            toggleUserMenuShow() {
                this.userMenuShow = !this.userMenuShow
            },

            async toggleChatNameEditing() {
                this.editingChatName = true
                await this.$nextTick()
                this.$refs.chatname.focus()
                this.updateWidth();
            },

            updateWidth() {
                this.$refs.chatname.style.width = (this.$refs.chatname.value.length + 1) + 'ch'
                if (this.$refs.chatname.scrollWidth > this.$refs.chatname.offsetWidth) {
                    this.$refs.chatname.style.width = '100%'
                }
            },

            showChatNameSaved() {
                this.chatNameSaved = true;
                setTimeout(() => this.chatNameSaved = false, 3000);
            },
        }))
    })
</script>
