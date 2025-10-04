<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profil specjalisty') }}
        </h2>
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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Kolumna na zdjęcie (placeholder) --}}
                        <div class="md:col-span-1 flex justify-center items-start">
                            <div class="w-48 h-48 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                <span class="text-gray-500">{{ __('Zdjęcie') }}</span>
                            </div>
                        </div>

                        {{-- Kolumna na informacje --}}
                        <div class="md:col-span-2">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $specialist->name }}</h3>
                            <p class="mt-1 text-md text-indigo-600 dark:text-indigo-400">{{ $specialist->specialization }}</p>

                            {{-- Przyciski akcji --}}
                            @auth
                            <div class="mt-4 flex items-center gap-x-4">
                                <a href="{{ route('specialists.edit', $specialist) }}" class="rounded-md bg-yellow-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-400">
                                    Edytuj
                                </a>
                                <form action="{{ route('specialists.destroy', $specialist) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć tego specjalistę?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                                        Usuń
                                    </button>
                                </form>
                            </div>
                            @endauth

                            <div class="mt-6 border-t border-gray-200 dark:border-gray-700">
                                <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Adres email
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                            <a href="mailto:{{ $specialist->email }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">{{ $specialist->email }}</a>
                                        </dd>
                                    </div>
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Opis
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                            {{ $specialist->description ?? 'Brak opisu.' }}
                                        </dd>
                                    </div>
                                    {{-- Można tu dodać więcej pól w przyszłości --}}
                                </dl>
                            </div>
                        </div>
                    </div>

                    {{-- Przycisk powrotu --}}
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-start">
                        <a href="{{ route('specialists.index') }}" class="rounded-md bg-gray-200 px-3 py-2 text-sm font-semibold text-gray-800 shadow-sm hover:bg-gray-300">
                            &laquo; Powrót do listy
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
