<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edytuj profil specjalisty') }}
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

                    <form method="POST" action="{{ route('specialists.update', $specialist) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Imię i Nazwisko (readonly) --}}
                        <div>
                            <x-input-label for="name" :value="__('Imię i nazwisko')" />
                            <x-text-input id="name" class="block mt-1 w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed" type="text" name="name" :value="$specialist->name" readonly />
                             <p class="mt-2 text-sm text-gray-500">Imienia i nazwiska nie można edytować w tym formularzu.</p>
                        </div>
                        
                        {{-- Email --}}
                        <div>
                            <x-input-label for="email" :value="__('Adres email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $specialist->email)" required autocomplete="email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        {{-- Specjalizacja --}}
                        <div>
                            <x-input-label for="specialization" :value="__('Specjalizacja')" />
                            <x-text-input id="specialization" class="block mt-1 w-full" type="text" name="specialization" :value="old('specialization', $specialist->specialization)" required />
                            <x-input-error :messages="$errors->get('specialization')" class="mt-2" />
                        </div>

                        {{-- Opis --}}
                        <div>
                            <x-input-label for="description" :value="__('Opis')" />
                            <textarea id="description" name="description" rows="4"
                                      class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $specialist->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            <p class="mt-2 text-sm text-gray-500">Opisz swoje doświadczenie, podejście do pacjenta itp.</p>
                        </div>

                        {{-- Przyciski akcji --}}
                        <div class="mt-6 flex items-center justify-end gap-x-4">
                            <a href="{{ route('specialists.show', $specialist) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Anuluj') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Zapisz zmiany') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
