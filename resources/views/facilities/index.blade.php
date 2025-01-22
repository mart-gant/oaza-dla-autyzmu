<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facilities</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <h1>Facilities</h1>
    <ul>
        @foreach($facilities as $facility)
            <li>
                <strong>Name:</strong> {{ $facility->name }}<br>
                <strong>Address:</strong> {{ $facility->address }}<br>
                <strong>Phone:</strong> {{ $facility->phone }}<br>
                <strong>Email:</strong> {{ $facility->email }}<br>
                <strong>Description:</strong> {{ $facility->description }}
                <strong>WWW:</strong> {{ $facility->www }}
            </li>
        @endforeach
    </ul>
    <div class="pagination">
        {{ $facilities->links() }}
    </div>
</body>
</html>
