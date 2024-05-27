<nav x-data="mainmenumobile" class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        @foreach($menu as $group)
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    @foreach($group as $item)
                        <li>
                            <a href="{{ route($item['route'], $item['route-params']) }}" class="@if($item['current']) bg-gray-50 text-blue-600 @else text-gray-700 hover:text-blue-600 hover:bg-gray-50 @endif group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                {!! $item['icon'] !!}
                                {{ $item['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</nav>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mainmenumobile', () => ({
            chatUuid: @entangle('chatUuid'),

            init () {
                window.addEventListener('chatnamesaved', (e) => {
                    this.$wire.loadChatMenu()
                })
            },
        }))
    })
</script>
