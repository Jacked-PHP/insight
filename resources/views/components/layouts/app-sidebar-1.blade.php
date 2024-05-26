<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head class="h-full bg-white">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen h-full font-sans antialiased bg-white">

<div x-data="layoutsidebarone">
    <div x-cloak class="relative z-50 lg:hidden" x-show="menuShow" role="dialog" aria-modal="true">
        <div
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-show="menuShow"
            class="fixed inset-0 bg-gray-900/80"
        ></div>

        <div class="fixed inset-0 flex">
            <div
                x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                x-show="menuShow"
                class="relative mr-16 flex w-full max-w-xs flex-1"
                @click.away="toggleMenu()"
            >
                <div
                    x-transition:enter="ease-in-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in-out duration-300"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    x-show="menuShow"
                    class="absolute left-full top-0 flex w-16 justify-center pt-5"
                >
                    <button @click="toggleMenu()" type="button" class="-m-2.5 p-2.5">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Sidebar -->
                <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4">
                    <div class="flex h-16 shrink-0 items-center">
                        <img class="h-8 w-auto" src="{{ asset('imgs/ollama.png') }}" alt="Ollama Client">
                    </div>
                    @livewire('main-menu', ['device' => 'mobile'])
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar for desktop -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
        <!-- Sidebar -->
        <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 pb-4">
            <div class="flex h-16 shrink-0 items-center">
                <img class="h-8 w-auto" src="{{ asset('imgs/ollama.png') }}" alt="Ollama Client">
            </div>
            @livewire('main-menu', ['device' => 'desktop'])
        </div>
    </div>

    <!-- Top Menu -->
    <div class="lg:pl-72">
        <div class="sticky top-0 z-40 lg:mx-auto lg:max-w-7xl lg:px-8">
            <div class="flex h-16 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-0 lg:shadow-none">
                <button @click="toggleMenu()" type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Separator -->
                <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

                @livewire('user-menu')
            </div>
        </div>

        <main class="">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        Alpine.data('layoutsidebarone', () => ({
            menuShow: false,
            userMenuShow: false,

            toggleMenu() {
                this.menuShow = !this.menuShow
            },

            toggleUserMenuShow() {
                this.userMenuShow = !this.userMenuShow
            },
        }))

        Alpine.store('chatData', {
            chatUuid: null,
            chatName: '',

            setChatUuid(uuid) {
                this.chatUuid = uuid
            },

            setChatName(name) {
                this.chatName = name
            },
        })
    })
</script>
</body>
</html>
