@extends('admin.layout')
@section('title', $match->exists ? 'Edit Match' : 'New Match')
@section('heading', $match->exists ? 'म्याच सम्पादन' : 'नयाँ म्याच')

@section('content')
    <div class="breadcrumb"><a href="{{ route('admin.matches.index') }}">म्याचहरू</a> / {{ $match->exists ? 'Edit' : 'New' }}</div>

    <form method="POST" action="{{ $match->exists ? route('admin.matches.update', $match) : route('admin.matches.store') }}">
        @csrf
        @if ($match->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-row">
                <label>प्रतियोगिता *</label>
                <select name="tournament_id" required>
                    <option value="">छान्नुहोस्</option>
                    @foreach ($tournaments as $t)
                        <option value="{{ $t->id }}" @selected(old('tournament_id', $match->tournament_id) === $t->id)>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>Home Team *</label>
                    <select name="home_team_id" required>
                        <option value="">छान्नुहोस्</option>
                        @foreach ($teams as $t)
                            <option value="{{ $t->id }}" @selected(old('home_team_id', $match->home_team_id) === $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>Away Team *</label>
                    <select name="away_team_id" required>
                        <option value="">छान्नुहोस्</option>
                        @foreach ($teams as $t)
                            <option value="{{ $t->id }}" @selected(old('away_team_id', $match->away_team_id) === $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>Home Score</label>
                    <input type="number" name="home_score" value="{{ old('home_score', $match->home_score) }}">
                </div>
                <div class="form-row">
                    <label>Away Score</label>
                    <input type="number" name="away_score" value="{{ old('away_score', $match->away_score) }}">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label>Match Date *</label>
                    <input type="datetime-local" name="match_date" value="{{ old('match_date', optional($match->match_date)->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div class="form-row">
                    <label>Status</label>
                    <select name="status">
                        @foreach (['UPCOMING','LIVE','FT','HT','POSTPONED','CANCELLED'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $match->status ?? 'UPCOMING') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <label>Venue</label>
                <input type="text" name="venue" value="{{ old('venue', $match->venue) }}">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ $match->exists ? 'अपडेट' : 'सिर्जना' }}</button>
            <a href="{{ route('admin.matches.index') }}" class="btn">रद्द</a>
        </div>
    </form>
@endsection