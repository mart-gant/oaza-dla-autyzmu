@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $category->name }}</h1>
    <ul>
        @foreach ($category->topics as $topic)
            <li>
                <a href="{{ route('forum.topic', $topic->id) }}">{{ $topic->title }}</a>
                @if(auth()->check() && auth()->id() === $topic->user_id)
                    <a href="{{ route('forum.editTopic', $topic->id) }}"></a>
                    <form action="{{ route('forum.deleteTopic', $topic->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit"></button>
                    </form>
                @endif
            </li>
        @endforeach
    </ul>
    {{ $category->topics->links() }}

    <h2>Dodaj temat</h2>
    <form action="{{ route('forum.storeTopic') }}" method="POST">
        @csrf
        <input type="hidden" name="category_id" value="{{ $category->id }}">
        <input type="text" name="title" placeholder="Tytuł tematu" required>
        <button type="submit">Dodaj</button>
    </form>
</div>
@endsection
