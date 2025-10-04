<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $topic->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">
                        <p>{{ $topic->posts->first()->body }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('Autor:') }} {{ $topic->user->name }}</p>
                        @can('update', $topic)
                            <a href="{{ route('forum.edit', $topic) }}" class="text-sm text-gray-600 dark:text-gray-400">{{ __('Edytuj') }}</a>
                        @endcan
                    </div>

                    <div class="mt-4">
                        @forelse ($posts->skip(1) as $post)
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                <p>{{ $post->body }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('Autor:') }} {{ $post->user->name }}</p>
                                @can('update', $post)
                                    <a href="{{ route('forum.post.edit', $post) }}" class="text-sm text-gray-600 dark:text-gray-400">{{ __('Edytuj') }}</a>
                                @endcan
                                @can('delete', $post)
                                    <form action="{{ route('forum.post.destroy', $post) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 dark:text-red-400">{{ __('Usuń') }}</button>
                                    </form>
                                @endcan
                            </div>
                        @empty
                            <p>{{ __('Brak odpowiedzi w tym temacie.') }}</p>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>

                    @auth
                        <div class="mt-4">
                            <form action="{{ route('forum.post.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="forum_topic_id" value="{{ $topic->id }}">
                                <div>
                                    <x-input-label for="body" :value="__('Odpowiedz')" />
                                    <textarea id="body" name="body" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('body') }}</textarea>
                                    <x-input-error :messages="$errors->get('body')" class="mt-2" />
                                </div>
                                <div class="flex items-center justify-end mt-4">
                                    <x-primary-button class="ml-3">
                                        {{ __('Odpowiedz') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
