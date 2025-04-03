@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Poradnik wiedzy</h1>
    <ul>
        @foreach ($articles as $article)
            <li>
                <a href="{{ route('articles.show', $article->id) }}">{{ $article->title }}</a>
                @if(auth()->check() && auth()->id() === $article->user_id)
                    <a href="{{ route('articles.edit', $article->id) }}">✏️</a>
                    <form action="{{ route('articles.destroy', $article->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">🗑</button>
                    </form>
                @endif
            </li>
        @endforeach
    </ul>
    {{ $articles->links() }}
</div>
@endsection
