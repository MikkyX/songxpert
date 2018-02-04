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
            background: #7da8c3;
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

        form, .hero {
            margin-bottom: 2em;
        }
    </style>
</head>
<body>
    <div id="h-v-center">
        <div class="container">
            <div class="columns">
                <div class="column is-half-desktop is-offset-one-quarter-desktop has-text-centered">
                    <div class="box">
                        <h1 class="title">
                            <i class="fab fa-2x fa-spotify"></i><br />
                            Name That Tune!
                        </h1>

                        <progress class="progress is-info" id="playtime" max="30" value="0"></progress>

                        @if ($update == 'Right')
                            <section class="hero is-success has-text-left">
                                <div class="hero-body">
                                    <div class="container">
                                        <h1 class="title"><i class="far fa-check-circle"></i> Right!</h1>
                                        <h2 class="subtitle">You scored <strong>{{ $score }}</strong> points for this one</h2>
                                    </div>
                                </div>
                            </section>
                        @endif

                        @if ($update == 'Wrong')
                            <section class="hero is-danger has-text-left">
                                <div class="hero-body">
                                    <div class="container">
                                        <h1 class="title"><i class="far fa-exclamation-circle"></i> Wrong!</h1>
                                    </div>
                                </div>
                            </section>
                        @endif

                        <form action="/guess" method="post">
                            {!! csrf_field() !!}
                            <input id="time" name="time" type="hidden" value="" />
                            <audio id="song">
                                <source src="{!! $track->preview_url !!}" type="audio/mp3">
                            </audio>

                            <h3>Is this....</h3>
                            @foreach ($answers as $answer)
                                <button
                                        class="button is-success"
                                        name="answer"
                                        type="submit"
                                        value="{{ $answer->track->id }}"
                                ><i class="far fa-music"></i>&nbsp;&nbsp;{{ str_limit($answer->track->name,30) }} - {{ str_limit(collect($answer->track->artists)->implode('name',', '),30) }}&nbsp;&nbsp;<i class="far fa-music"></i></button>
                            @endforeach
                        </form>

                        <p>Created for <a href="https://larahack.com" target="_blank">Larahack 2018</a> by <a href="https://twitter.com/mikkyx">mikkyx</a></p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Update the form to show how far into the song we are
        setInterval(function() {
            elapsedTime = document.getElementById('song').currentTime;

            document.getElementById('playtime').value = elapsedTime;
            document.getElementById('time').value = elapsedTime;
        },10);

        // If the song ends without an answer being given, reload the page
        /*
        document.getElementById('song').onended = function() {
            location.reload();
        };
        */
    </script>
</body>
</html>