@use('App\Enums\Meta\AssetMetadata')

<div class=" flex flex-col gap-4" x-data="library">

    <div
        class="flex flex-col items-center justify-center py-8 bg-slate-200"
        x-on:drop="isDroppingFile = false"
        x-on:drop.prevent="handleFileDrop($event)"
        x-on:dragover.prevent="isDroppingFile = true"
        x-on:dragleave.prevent="isDroppingFile = false"
    >
        <div
            class="absolute top-0 bottom-0 left-0 right-0 z-30 flex items-center justify-center bg-blue-500 opacity-90"
            x-show="isDropping"
        ><span class="text-3xl text-white">Release file to upload!</span></div>

        <label class="flex flex-col items-center justify-center w-2/3 bg-white border shadow cursor-pointer h-1/2 rounded-2xl hover:bg-slate-50 py-6" for="file-upload">
            <h3 class="text-xl">Click here to select files to upload</h3>
            <em class="italic text-slate-400">(Or drag files to the page)</em>
            <div
                class="bg-blue-500 h-[2px]"
                style="transition: width 1s"
                :style="`width: ${progress}%;`"
                x-show="isUploading"
            ></div>
        </label>

        @if(count($files))
            <ul class="mt-5 mb-5 list-disc">
                @foreach($files as $file)
                    <li class="flex gap-2 items-center">
                        {{$file->getClientOriginalName()}}
                        <button class="text-red-500" @click="removeUpload('{{$file->getFilename()}}')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </li>
                @endforeach
            </ul>
            <button wire:click="saveUploadedFiles()" type="button" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Save Files</button>
        @endif

        <input type="file" id="file-upload" multiple @change="handleFileSelect" class="hidden" />
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        @foreach($assets as $asset)
            <div class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:border-gray-400">
                <div class="flex-shrink-0">
                    @if($asset->hasMeta(AssetMetadata::CONTENT->value))
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
                    <a href="{{ route('library-asset', $asset->id) }}" class="focus:outline-none">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        <p class="text-sm font-medium text-gray-900">{{ $asset->name }}</p>
                        {{--<p class="truncate text-sm text-gray-500">Co-Founder / CEO</p>--}}
                    </a>
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
                this.$wire.on('file-saved', (e) => this.$dispatch('notify', {
                    message: e[0] ?? '',
                    type: 'success',
                }));
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
