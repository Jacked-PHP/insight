<div class="pt-2" x-data="remotelibrary">
    <div class="bg-white border shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-base font-semibold text-gray-900">Add a path:</h3>
            <div class="mt-2 max-w-xl text-sm text-gray-500">
                <p>This specifies the path where the system will reach for documents.</p>
            </div>
            <form wire:submit="addVault" class="mt-5 sm:flex sm:items-center">
                <div class="w-full sm:max-w-xs">
                    <label for="path" class="sr-only">Path</label>
                    <input type="text" name="path" id="path" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm/6" placeholder="/path/to/vault" wire:model="path">
                </div>
                <button wire:click="openFolderDialog" type="button" class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:ml-3 sm:mt-0 sm:w-auto">Find Folder</button>
                <button type="submit" class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:ml-3 sm:mt-0 sm:w-auto">Add</button>
            </form>
        </div>
    </div>

    <hr class="mt-4 mb-4"/>

    <div class="flex flex-col gap-2">
        @forelse($vaults as $vault)
            <div class="px-6 py-4 rounded-lg border flex gap-4 items-center justify-between">
                <div class="flex gap-4 items-center">
                    <div class="border border-gray-300 bg-gray-100 px-2 py-1 rounded-lg">{{ $vault->path }}</div>
                    <a href="{{ route('vault', $vault->id) }}" type="button" class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:ml-3 sm:mt-0 sm:w-auto">Open Library</a>
                    <span>{{ $vault->assets->count() }} files</span>
                    <span>({{ $vault->assets()->indexed()->count() }} embedded)</span>
                </div>
                <div class="h-full flex items-center">
                    <button wire:click="deleteVault({{ $vault->id }})"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
                </div>
            </div>
        @empty
            <div class="italic">Empty</div>
        @endforelse
    </div>
</div>

<script>

    document.addEventListener('alpine:init', () => {
        Alpine.data('remotelibrary', () => ({

            init() {
                let $this = this;

                if (typeof Native !== "undefined") Native.on("App\\Events\\AssetIndexed", (e) => {
                    $this.$dispatch('notify', {
                        message: 'Asset Indexed: ' + e.name,
                        type: 'success',
                    });
                    $this.$wire.$refresh();
                });
            },
        }));
    });

</script>
