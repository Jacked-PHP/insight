<div class="pt-2">
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
                <button type="submit" class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:ml-3 sm:mt-0 sm:w-auto">Add</button>
            </form>
        </div>
    </div>

    @foreach($vaults as $vault)
        <div>{{ $vault->path }}</div>
    @endforeach
</div>
