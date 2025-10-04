<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Szczegóły placówki') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Session Status -->
            @if(session('success'))
                <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    {{-- Nazwa placówki i przyciski akcji --}}
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $facility->name }}</h3>
                        
                        @auth
                        <div class="flex items-center gap-x-4">
                            <a href="{{ route('facilities.edit', $facility->id) }}" class="rounded-md bg-yellow-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-400">
                                Edytuj
                            </a>
                            <form action="{{ route('facilities.destroy', $facility) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć tę placówkę?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                                    Usuń
                                </button>
                            </form>
                        </div>
                        @endauth
                    </div>
                    
                    {{-- Placeholder na mapę/zdjęcie --}}
                    <div class="mt-4 h-64 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                         <span class="text-gray-500">{{ __('Miejsce na mapę lub zdjęcie') }}</span>
                    </div>

                    {{-- Sekcja ze szczegółami --}}
                    <div class="mt-6 border-t border-gray-200 dark:border-gray-700">
                        <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Adres
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                    {{ $facility->address }}
                                </dd>
                            </div>
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Telefon
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                    {{ $facility->phone_number ?? 'Brak numeru telefonu.' }}
                                </dd>
                            </div>
                             <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Strona internetowa
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                     <a href="{{ $facility->website ?? '#' }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">{{ $facility->website ?? 'Brak strony internetowej.' }}</a>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Przycisk powrotu --}}
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-start">
                        <a href="{{ route('facilities.index') }}" class="rounded-md bg-gray-200 px-3 py-2 text-sm font-semibold text-gray-800 shadow-sm hover:bg-gray-300">
                            &laquo; Powrót do listy
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
