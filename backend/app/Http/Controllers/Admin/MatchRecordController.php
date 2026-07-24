<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchRecord;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchRecordController extends Controller
{
    public function index(Request $request): View
    {
        $query = MatchRecord::query()
            ->with(['tournament:id,name', 'homeTeam:id,name', 'awayTeam:id,name']);

        if ($tournamentId = $request->query('tournament')) {
            $query->where('tournament_id', $tournamentId);
        }

        $matches = $query->orderByDesc('match_date')->paginate(30)->withQueryString();
        $tournaments = Tournament::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.matches.index', compact('matches', 'tournaments'));
    }

    public function create(): View
    {
        return view('admin.matches.form', [
            'match' => new MatchRecord(),
            'tournaments' => Tournament::query()->orderBy('name')->get(['id', 'name']),
            'teams' => Team::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateData($request);
        $match = MatchRecord::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'Match', $match->id, newValue: $match->toArray());

        return redirect()->route('admin.matches.index')->with('status', 'म्याच थपियो।');
    }

    public function edit(MatchRecord $match): View
    {
        return view('admin.matches.form', [
            'match' => $match,
            'tournaments' => Tournament::query()->orderBy('name')->get(['id', 'name']),
            'teams' => Team::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, AuditService $audit, MatchRecord $match): RedirectResponse
    {
        $data = $this->validateData($request);
        $old = $match->toArray();
        $match->update($data);
        $audit->record($request->user(), 'UPDATE', 'Match', $match->id, oldValue: $old, newValue: $match->fresh()->toArray());

        return redirect()->route('admin.matches.index')->with('status', 'म्याच अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, MatchRecord $match): RedirectResponse
    {
        $old = $match->toArray();
        $match->delete();
        $audit->record($request->user(), 'DELETE', 'Match', $match->id, oldValue: $old);

        return redirect()->route('admin.matches.index')->with('status', 'म्याच मेटाइयो।');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'tournament_id' => ['required', 'string', 'exists:tournaments,id'],
            'home_team_id' => ['required', 'string', 'exists:teams,id', 'different:away_team_id'],
            'away_team_id' => ['required', 'string', 'exists:teams,id'],
            'match_date' => ['required', 'date'],
            'venue' => ['nullable', 'string', 'max:200'],
            'status' => ['nullable', 'string', 'max:30'],
            'home_score' => ['nullable', 'integer'],
            'away_score' => ['nullable', 'integer'],
        ]) + [
            'status' => $request->input('status', 'UPCOMING'),
        ];
    }
}