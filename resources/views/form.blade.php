<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Name That Tune [ Larahack 2018 ]</title>
</head>
<body>
    <h1>Name That Tune!</h1>
    <form action="/guess" method="post">
        {!! csrf_field() !!}
        <audio autoplay>
            <source src="{!! $track->preview_url !!}" type="audio/mp3">
        </audio>
        Is this....
        @foreach ($answers as $answer)
            <button
                name="answer"
                type="submit"
                value="{{ $answer->track->id }}"
            >{{ $answer->track->name }} ({{ collect($answer->track->artists)->first()->name }})</button>
        @endforeach
    </form>
    <p>Created for <a href="https://larahack.com" target="_blank">Larahack 2018</a></p>
</body>
</html>