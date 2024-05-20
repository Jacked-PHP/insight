<div class="mx-auto h-screen max-w-7xl px-4 sm:px-6 lg:px-8 flex justify-center">
    <div class="mx-auto max-w-xl h-screen flex flex-grow items-center">
        <div class="flex min-h-full flex-col w-full justify-center py-12 sm:px-6 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <img class="mx-auto h-10 w-auto" src="{{ asset('imgs/ollama.png') }}" alt="Ollama Client">
                <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Register</h2>
            </div>

            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
                <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
                    @if($userRegistered)
                        <div class="text-green-600 mb-6">User registered successfully. Please check your email for verification.</div>
                    @else
                        <form class="space-y-6" action="#" method="POST" wire:submit="register">
                            <div>
                                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                                <div class="mt-2">
                                    <input wire:model="name" id="name" name="name" type="text" autocomplete="name" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                @error('name') <span class="text-red-600 mb-6">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                                <div class="mt-2">
                                    <input wire:model="email" id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                @error('email') <span class="text-red-600 mb-6">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                                <div class="mt-2">
                                    <input wire:model="password" id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                @error('password') <span class="text-red-600 mb-6">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input wire:model="acceptTerms" id="acceptTerms" name="acceptTerms" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    <label for="remember-me" class="ml-3 block text-sm leading-6 text-gray-900">Accept <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-500">Terms</a></label>
                                </div>
                                @error('acceptTerms') <span class="text-red-600 mb-6">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Register</button>
                            </div>
                        </form>
                    @endif
                </div>

                <p class="mt-10 text-center text-sm text-gray-500">
                    <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Sign In here</a>
                </p>
            </div>
        </div>
    </div>
</div>
