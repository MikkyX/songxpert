<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Name That Tune [ Larahack 2018 ]</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.6.2/css/bulma.css" rel="stylesheet" type="text/css">
    <script src="/js/fontawesome.min.js"></script>
    <script src="/js/fa-regular.min.js"></script>
</head>
<body>
    <h1>Name That Tune!</h1>
    @if ($update)
        {{ $update }}
    @endif
    <form action="/guess" method="post">
        {!! csrf_field() !!}
        <input id="time" name="time" type="hidden" value="" />
        <audio autoplay id="song">
            <source src="{!! $track->preview_url !!}" type="audio/mp3">
        </audio>
        Is this....
        @foreach ($answers as $answer)
            <button
                name="answer"
                type="submit"
                value="{{ $answer->track->id }}"
            >{{ $answer->track->name }} - {{ collect($answer->track->artists)->first()->name }}</button>
        @endforeach
    </form>
    <p>Created for <a href="https://larahack.com" target="_blank">Larahack 2018</a> by <a href="https://twitter.com/mikkyx">mikkyx</a></p>

    <script>
        // Update the form to show how far into the song we are
        setInterval(function() {
            console.log('hello');
            document.getElementById('time').value = document.getElementById('song').currentTime;
        },100);
    </script>
</body>
</html>