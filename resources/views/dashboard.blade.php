<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel główny') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __("Witaj w panelu!") }}</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __("Poniżej znajdziesz szybkie skróty do najważniejszych sekcji aplikacji.") }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Karta Placówki -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-transform transform hover:-translate-y-1">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ __('Placówki') }}</h4>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Zarządzaj listą placówek medycznych.') }}</p>
                        <div class="mt-4">
                            <a href="{{ route('facilities.index') }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                {{ __('Przejdź do placówek') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Karta Specjaliści -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-transform transform hover:-translate-y-1">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ __('Specjaliści') }}</h4>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Przeglądaj i zarządzaj specjalistami.') }}</p>
                        <div class="mt-4">
                            <a href="{{ route('specialists.index') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                {{ __('Przejdź do specjalistów') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Karta Forum -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-transform transform hover:-translate-y-1">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ __('Forum') }}</h4>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Uczestnicz w dyskusjach na forum.') }}</p>
                        <div class="mt-4">
                            <a href="{{ route('forum.categories') }}" class="inline-block bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded">
                                {{ __('Przejdź do forum') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Karta Moje Wizyty -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-transform transform hover:-translate-y-1">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ __('Moje wizyty') }}</h4>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Przeglądaj i zarządzaj swoimi umówionymi wizytami.') }}</p>
                        <div class="mt-4">
                            <a href="{{ route('my-visits') }}" class="inline-block bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded">
                                {{ __('Przejdź do moich wizyt') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
