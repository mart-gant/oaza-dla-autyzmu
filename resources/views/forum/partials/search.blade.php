<form action="{{ route('forum.index', $category) }}" method="GET">
    <div class="flex items-center">
        <x-text-input type="text" name="search" placeholder="Szukaj w tematyce..." value="{{ $search ?? '' }}" class="w-full"/>
        <x-primary-button class="ml-2">{{ __('Szukaj') }}</x-primary-button>
    </div>
</form>
