@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Recenzje dla {{ $facility->name }}</h1>
    <ul>
        @foreach ($reviews as $review)
            <li>
                <strong>{{ $review->user->name }}</strong> – {{ $review->rating }}/5
                <p>{{ $review->comment }}</p>
                @if(auth()->check() && auth()->id() === $review->user_id)
                    <a href="{{ route('reviews.edit', $review->id) }}">✏️</a>
                    <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">🗑</button>
                    </form>
                @endif
            </li>
        @endforeach
    </ul>
    {{ $reviews->links() }}

    <h2>Dodaj recenzję</h2>
    <form action="{{ route('reviews.store') }}" method="POST">
        @csrf
        <input type="hidden" name="facility_id" value="{{ $facility->id }}">
        <label>Ocena:</label>
        <select name="rating" required>
            <option value="5">5 - Świetnie</option>
            <option value="4">4 - Dobrze</option>
            <option value="3">3 - Średnio</option>
            <option value="2">2 - Słabo</option>
            <option value="1">1 - Bardzo źle</option>
        </select>
        <textarea name="comment" placeholder="Dodaj komentarz"></textarea>
        <button type="submit">Dodaj recenzję</button>
    </form>
</div>
@endsection
