@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Forum</h1>
    <ul>
        @foreach ($categories as $category)
            <li>
                <a href="{{ route('forum.category', $category->id) }}">{{ $category->name }}</a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
