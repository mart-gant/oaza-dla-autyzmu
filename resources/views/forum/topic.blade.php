@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $topic->title }}</h1>
    <ul>
        @foreach ($topic->posts as $post)
            <li>
                <strong>{{ $post->user->name }}</strong>: {{ $post->content }}
                @if(auth()->check() && auth()->id() === $post->user_id)
                    <a href="{{ route('forum.editPost', $post->id) }}">✏️</a>
                    <form action="{{ route('forum.deletePost', $post->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">🗑</button>
                    </form>
                @endif
            </li>
        @endforeach
    </ul>
    {{ $topic->posts->links() }}

    <h2>Dodaj odpowiedź</h2>
    <form action="{{ route('forum.storePost') }}" method="POST">
        @csrf
        <input type="hidden" name="topic_id" value="{{ $topic->id }}">
        <textarea name="content" placeholder="Treść wiadomości" required></textarea>
        <button type="submit">Odpowiedz</button>
    </form>
</div>
@endsection
