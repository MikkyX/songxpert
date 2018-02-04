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
    <script src="/js/fa-brands.min.js"></script>
    <script src="/js/fa-regular.min.js"></script>
    <style>
        html, body {
            background: #f5f8fa;
            height: 100%;
        }

        #h-v-center {
            align-items: center;
            display: flex;
            justify-content: center;
            height: 100%;
        }

        button {
            display: block;
            margin-top: 1em;
            width: 100%;
        }
    </style>
</head>
<body>
    <form action="/logout" id="logoutform" method="post">
        {!! csrf_field() !!}
    </form>
    <audio id="song">
        <source src="{!! $track->preview_url !!}" type="audio/mp3">
    </audio>
    <div id="h-v-center">
        <div class="container">
            <div class="columns">
                <div class="column">
                    <div class="box">
                        <nav class="navbar">
                            <div class="navbar-brand">
                                <span class="navbar-item is-size-3"><i class="fab fa-spotify"></i>&nbsp;&nbsp;Name That Tune!</span>
                            </div>
                            <div class="navbar-menu">
                                <div class="navbar-end">
                                    <a class="navbar-item" href="/leaderboard">Leaderboard</a>
                                    @if (Auth::check())
                                        <div class="navbar-item">{{ Auth::user()->name }}</div>
                                        <div class="navbar-item">
                                            <input class="button is-danger" form="logoutform" type="submit" value="Log Out">
                                        </div>
                                    @else
                                        <span class="navbar-item"><a class="button is-info" href="/register">Register</a></span>
                                        <span class="navbar-item"><a class="button is-primary" href="/login">Log In</a></span>
                                    @endif
                                </div>
                            </div>
                        </nav>
                        <hr />

                        @if ($update == 'Right')
                            <div class="message is-success has-text-centered">
                                <div class="message-body">
                                    <i class="far fa-check-circle"></i> <strong>Right!</strong>
                                    - You scored <strong>{{ $last_score }}</strong> points for this one
                                </div>
                            </div>
                        @endif

                        @if ($update == 'Wrong')
                            <div class="message is-danger has-text-centered">
                                <div class="message-body">
                                    <i class="far fa-exclamation-circle"></i> <strong>Wrong!</strong>
                                    - Can't win 'em all... ¯\_(ツ)_/¯
                                </div>
                            </div>
                        @endif

                        @if ($update == 'Timeout')
                            <div class="message is-dark has-text-centered">
                                <div class="message-body">
                                    <i class="far fa-clock"></i> <strong>Timed Out!</strong>
                                    - Don't forget to guess the next one...
                                </div>
                            </div>
                        @endif

                        <form action="/guess" method="post">
                            {!! csrf_field() !!}
                            <input id="time" name="time" type="hidden" value="" />
                            <div class="columns">
                                <div class="column has-text-centered">
                                    <p>Answer now and you'll get <strong id="score"></strong> points!</p>
                                    <progress class="progress is-info" id="playtime" max="30" value="0"></progress>

                                    @if (Auth::check())
                                        <div class="columns">
                                            <div class="column">
                                                Right:<br />
                                                <span class="is-size-1">{{ Auth::user()->songs_correct }}</span> / {{ Auth::user()->songs_seen }}</small>
                                            </div>
                                            <div class="column">
                                                Score:<br />
                                                <span class="is-size-1">{{ Auth::user()->score }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="columns">
                                            <div class="column">
                                                <p>Want to store your stats and appear on the leaderboard?</p>
                                                <p><a class="button is-info" href="/register">Register</a> or <a class="button is-primary" href="/login">Log In</a></p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="column">
                                    <h3>Is this....</h3>
                                    @foreach ($answers as $answer)
                                        <button
                                                class="button is-success"
                                                name="answer"
                                                type="submit"
                                                value="{{ $answer->track->id }}"
                                        ><i class="far fa-music"></i>&nbsp;&nbsp;{{ str_limit($answer->track->name,25) }} - {{ str_limit(collect($answer->track->artists)->implode('name',', '),25) }}&nbsp;&nbsp;<i class="far fa-music"></i></button>
                                    @endforeach
                                </div>
                            </div>
                        </form>
                        <div class="columns">
                            <div class="column has-text-centered">
                                <p>Created for <a href="https://larahack.com" target="_blank">Larahack 2018</a> by <a href="https://twitter.com/mikkyx">mikkyx</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update the form to show how far into the song we are
        setInterval(function() {
            // Find out how much of the sample has played
            elapsedTime = document.getElementById('song').currentTime;

            // Update the progress bar and form field
            document.getElementById('playtime').value = elapsedTime;
            document.getElementById('time').value = elapsedTime;

            // Update the score banner
            scoreNow = Math.ceil((30 - elapsedTime) / 6);
            document.getElementById('score').innerText = scoreNow;
        },10);

        // If the song ends without an answer being given, reload the page
        document.getElementById('song').onended = function() {
            location.href = '/timeout';
        };
    </script>
</body>
</html>