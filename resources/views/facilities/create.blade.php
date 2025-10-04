<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dodaj nową placówkę') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Display Errors --}}
                    @if ($errors->any())
                        <div class="mb-4">
                            <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>

                            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('facilities.store') }}" class="space-y-6">
                        @csrf

                        {{-- Nazwa placówki --}}
                        <div>
                            <x-input-label for="name" :value="__('Nazwa placówki')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        {{-- Adres --}}
                        <div>
                            <x-input-label for="address" :value="__('Adres')" />
                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        {{-- Miasto --}}
                        <div>
                            <x-input-label for="city" :value="__('Miasto')" />
                            <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" required />
                            <x-input-error :messages="$errors->get('city')" class="mt-2" />
                        </div>

                        {{-- Województwo --}}
                        <div>
                            <x-input-label for="province" :value="__('Województwo')" />
                            <x-text-input id="province" class="block mt-1 w-full" type="text" name="province" :value="old('province')" required />
                            <x-input-error :messages="$errors->get('province')" class="mt-2" />
                        </div>
                        
                        {{-- Numer telefonu --}}
                        <div>
                            <x-input-label for="phone_number" :value="__('Numer telefonu')" />
                            <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number')" />
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                        </div>
                        
                        {{-- Strona internetowa --}}
                        <div>
                            <x-input-label for="website" :value="__('Strona internetowa')" />
                            <x-text-input id="website" class="block mt-1 w-full" type="url" name="website" :value="old('website')" placeholder="https://example.com" />
                            <x-input-error :messages="$errors->get('website')" class="mt-2" />
                        </div>

                        {{-- Przyciski akcji --}}
                        <div class="mt-6 flex items-center justify-end gap-x-4">
                            <a href="{{ route('facilities.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Anuluj') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Utwórz placówkę') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
