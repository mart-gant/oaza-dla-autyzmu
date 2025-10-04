<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Forum') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">{{ __('Tematy') }}</h3>
                        @auth
                            <a href="{{ route('forum.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">{{ __('Nowy temat') }}</a>
                        @endauth
                    </div>
                    @include('forum.partials.search')

                    <div class="mt-4">
                        @forelse ($topics as $topic)
                            <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700">
                                <div>
                                    <a href="{{ route('forum.show', $topic) }}" class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ $topic->title }}</a>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('Autor:') }} {{ $topic->user->name }}</p>
                                </div>
                                @can('update', $topic)
                                    <a href="{{ route('forum.edit', $topic) }}" class="text-sm text-gray-600 dark:text-gray-400">{{ __('Edytuj') }}</a>
                                @endcan
                            </div>
                        @empty
                            <p>{{ __('Brak tematów do wyświetlenia.') }}</p>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $topics->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
