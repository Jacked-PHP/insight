@use('App\Enums\Meta\AssetMetadata')
@use('App\Enums\AssetType')

<div x-data="assetlibrary">

    <div class="p-4 pb-0">
        <a class="underline" href="{{ route('library') }}">Library</a>
        <span class="px-2">/</span>
        {{ $asset->name }}
    </div>

    <hr class="my-4"/>

    <div>
        <div class="flex gap-2 items-center">
            <button
                wire:click="indexDocument()"
                type="button"
                class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-not-allowed"
            >Index Content</button>
            <div wire:loading>
                Extracting Data...
            </div>
        </div>
    </div>

    <hr class="my-4"/>

    <button
        @click="tab = 'content'"
        type="button"
        class="rounded-md px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 flex-grow-0"
        :class="tab === 'content' ? 'bg-white' : 'bg-gray-200'"
    >Content</button>

    @if ($asset->type === AssetType::PDF)
        <button
            @click="tab = 'pages'"
            type="button"
            class="rounded-md px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 flex-grow-0"
            :class="tab === 'pages' ? 'bg-white' : 'bg-gray-200'"
        >Pages</button>
    @endif

    {{-- content --}}
    <div x-show="tab === 'content'" class="border border-gray-600 border-dashed bg-gray-50 p-4 flex flex-col gap-4 mt-1">
        <button
            @click="contentOpened = !contentOpened"
            type="button"
            class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 flex-grow-0"
            x-text="contentOpened ? 'Hide Content' : 'Show Content'"
        ></button>
        <hr/>
        <p x-show="contentOpened">{!! nl2br($asset->getMeta(AssetMetadata::CONTENT->value, '')) !!}</p>
    </div>

    {{-- pages --}}
    <div x-show="tab === 'pages'" class="border border-gray-600 border-dashed bg-gray-50 p-4 flex flex-col gap-4 mt-1">
        <button
            @click="pageOpened = !pageOpened"
            type="button"
            class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 flex-grow-0"
            x-text="pageOpened ? 'Hide Content' : 'Show Content'"
        ></button>
        <hr/>
        <div x-show="pageOpened">
            @foreach($pages as $page)
                <div><strong>Page:</strong> {{ $page->page }}</div>
                <div><strong>Path:</strong> {{ $page->path }}</div>
                <div><strong>Content:</strong> {{ $page->text }}</div>
                <hr class="my-4"/>
            @endforeach
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('assetlibrary', () => ({
            contentOpened: false,
            pageOpened: false,
            tab: 'content',

            init() {
                let $this = this;

                // listen to livewire event and dispatch notify
                window.addEventListener('success', (event) => {
                    $this.$dispatch('notify', {
                        message: event.detail.message,
                        type: 'success',
                    });
                });

                window.addEventListener('error', (event) => {
                    $this.$dispatch('notify', {
                        message: event.detail.message,
                        type: 'error',
                    });
                });

                // TODO: @deprecated
                if (typeof Native !== "undefined") Native.on("App\\Events\\DocumentContentExtractionRequested", () => {
                    $this.$dispatch('notify', {
                        message: 'Content extraction requested',
                        type: 'success',
                    });
                });

                // TODO: @deprecated
                if (typeof Native !== "undefined") Native.on("App\\Events\\DocumentContentExtractionRequested", () => {
                    $this.$dispatch('notify', {
                        message: 'Content extraction requested',
                        type: 'success',
                    });
                });

                // TODO: @deprecated
                if (typeof Native !== "undefined") Native.on("App\\Events\\DocumentContentExtracted", () => {
                    $this.$dispatch('notify', {
                        message: 'Content extraction finished',
                        type: 'success',
                    });
                    window.location.reload();
                });
            }
        }));
    });
</script>
