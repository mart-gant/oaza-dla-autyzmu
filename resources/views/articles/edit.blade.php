@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edytuj artykuł</h1>
    <form action="{{ route('articles.update', $article->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="title" value="{{ $article->title }}" required>
        <textarea name="content" required>{{ $article->content }}</textarea>
        <button type="submit">Zapisz</button>
    </form>
</div>
@endsection
