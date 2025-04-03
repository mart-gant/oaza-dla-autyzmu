<h1>Reakcje użytkowników</h1>

@if($reactions->isEmpty())
    <p>Brak reakcji.</p>
@else
    <ul>
        @foreach($reactions as $reaction)
            <li>
                <strong>{{ $reaction->user->name ?? 'Anonim' }}:</strong>
                {{ $reaction->content }}
                <em>({{ $reaction->created_at->diffForHumans() }})</em>
            </li>
        @endforeach
    </ul>
@endif
