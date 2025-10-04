<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Forum - Kategorie') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Przeglądaj kategorie dyskusji') }}
                    </h3>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($categories as $category)
                            <a href="{{ route('forum.category', $category->id) }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                                <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ $category->name }}</p>
                                {{-- W przyszłości można tu dodać opis kategorii --}}
                                {{-- <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $category->description }}</p> --}}
                            </a>
                        @empty
                            <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                                <p>{{ __('Nie znaleziono żadnych kategorii forum.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
