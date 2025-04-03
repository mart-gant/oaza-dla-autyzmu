@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edytuj temat</h1>
    <form action="{{ route('forum.updateTopic', $topic->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="title" value="{{ $topic->title }}" required>
        <button type="submit">Zapisz</button>
    </form>
</div>
@endsection
