<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Witaj w aplikacji!') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>Witaj w naszej aplikacji! Możesz teraz korzystać z następujących sekcji:</p>
                    <ul class="list-disc mt-4 ml-8">
                        <li><a href="{{ route('facilities.index') }}" class="underline hover:text-gray-400">Placówki</a></li>
                        <li><a href="{{ route('specialists.index') }}" class="underline hover:text-gray-400">Specjaliści</a></li>
                        <li><a href="{{ route('forum.categories') }}" class="underline hover:text-gray-400">Forum</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <footer class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
        Copyright by Marcin Gantkowski
    </footer>
</x-app-layout>
