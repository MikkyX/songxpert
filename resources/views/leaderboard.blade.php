@extends('master')
@section('content')
    <div class="columns">
        <div class="column">
            <h1 class="title">Leaderboard</h1>

            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Correct / Seen</th>
                    <th>Score</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($leaderboard as $row)
                    <tr {!! (Auth::id() == $row->id ? ' class="is-selected"' : '') !!}>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->songs_correct }} / {{ $row->songs_seen }}</td>
                        <td><strong>{{ $row->score }}</strong></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
@endsection