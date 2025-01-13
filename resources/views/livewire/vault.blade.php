@use('App\Enums\Meta\AssetMetadata')

<div class=" flex flex-col gap-4" x-data="library">

    <div class="">
        <a href="{{ route('remote-library') }}" type="button" class="mt-3 inline-flex items-center justify-center rounded-md bg-white border b-gray-800 px-3 py-2 text-sm font-semibold text-black shadow-sm hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600 w-auto">Back</a>
    </div>

    <div class="flex gap-4 p-4 border rounded-lg">
        <strong>Vault:</strong>
        <div>{{ $vault->path }}</div>
    </div>

    <div>
        <button wire:click="loadVaultsFiles" type="button" class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:ml-3 sm:mt-0 sm:w-auto">Load Files</button>
        <span wire:loading wire:target="loadVaultsFiles">Loading files...</span>

        @if($assets->count() > 0)
            <button wire:click="indexAssets" type="button" class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:ml-3 sm:mt-0 sm:w-auto">Index Assets</button>
        @endif

        @if($assets->count() > 0)
            <button wire:click="indexMissingAssets" type="button" class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:ml-3 sm:mt-0 sm:w-auto">Index Missing Assets</button>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        @foreach($assets as $asset)
            <div class="relative flex items-center space-x-3 rounded-lg border border-gray-300 px-6 py-5 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:border-gray-400 @if($asset->indexed_at !== null) bg-green-400 @else bg-white @endif">
                <div class="flex-shrink-0">
                    @if($asset->indexed_at)
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    {{--<a href="{{ route('library-asset', $asset->id) }}" class="focus:outline-none">--}}
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <p class="text-sm font-medium text-gray-900">{{ $asset->name }}</p>
                    {{--<p class="truncate text-sm text-gray-500">Co-Founder / CEO</p>--}}
                    {{--</a>--}}
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>

    document.addEventListener('alpine:init', () => {
        Alpine.data('library', () => ({
            isDropping: false,
            isUploading: false,
            progress: 0,

            init() {
                let $this = this;

                this.$wire.on('file-saved', (e) => this.$dispatch('notify', {
                    message: e[0] ?? '',
                    type: 'success',
                }));

                Native && Native.on("App\\Events\\AssetIndexed", (e) => {
                    $this.$dispatch('notify', {
                        message: 'Asset Indexed: ' + e.name,
                        type: 'success',
                    });
                    $this.$wire.$refresh();
                });
            },

            handleFileSelect(event) {
                if (event.target.files.length) {
                    this.uploadFiles(event.target.files)
                }
            },

            handleFileDrop(event) {
                if (event.dataTransfer.files.length > 0) {
                    this.uploadFiles(event.dataTransfer.files)
                }
            },

            uploadFiles(files) {
                const $this = this
                this.isUploading = true
                @this.uploadMultiple('files', files,
                    function (success) {
                        $this.isUploading = false
                        $this.progress = 0
                    },
                    function(error) {
                        $this.isUploading = false
                        $this.progress = 0
                        $this.$dispatch('notify', {
                            message: "Failed to upload files, try again later!",
                            type: 'error',
                        });

                        console.log('error', error)
                    },
                    function (event) {
                        $this.progress = event.detail.progress
                    }
                )
            },

            removeUpload(filename) {
                @this.removeUpload('files', filename)
            },
        }));
    });

</script>
