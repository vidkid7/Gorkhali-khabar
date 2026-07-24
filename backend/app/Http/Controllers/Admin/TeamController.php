<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        $teams = Team::query()
            ->withCount(['homeMatches', 'awayMatches'])
            ->orderBy('name')
            ->paginate(50);
        return view('admin.teams.index', compact('teams'));
    }

    public function create(): View
    {
        return view('admin.teams.form', ['team' => new Team()]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateData($request);
        $team = Team::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'Team', $team->id, newValue: $team->toArray());

        return redirect()->route('admin.teams.index')->with('status', 'टोली थपियो।');
    }

    public function edit(Team $team): View
    {
        return view('admin.teams.form', compact('team'));
    }

    public function update(Request $request, AuditService $audit, Team $team): RedirectResponse
    {
        $data = $this->validateData($request);
        $old = $team->toArray();
        $team->update($data);
        $audit->record($request->user(), 'UPDATE', 'Team', $team->id, oldValue: $old, newValue: $team->fresh()->toArray());

        return redirect()->route('admin.teams.index')->with('status', 'टोली अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, Team $team): RedirectResponse
    {
        $old = $team->toArray();
        $team->delete();
        $audit->record($request->user(), 'DELETE', 'Team', $team->id, oldValue: $old);

        return redirect()->route('admin.teams.index')->with('status', 'टोली मेटाइयो।');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'name_en' => ['nullable', 'string', 'max:120'],
            'logo' => ['nullable', 'string', 'max:500'],
        ]);
    }
}