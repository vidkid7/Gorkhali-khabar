<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\MatchRecord;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SportsController extends Controller
{
    public function index(): View
    {
        $tournaments = Tournament::query()->withCount('matches')->orderByDesc('start_date')->paginate(30);
        return view('admin.sports.index', compact('tournaments'));
    }

    public function create(): View
    {
        return view('admin.sports.form', ['tournament' => new Tournament()]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sport_type' => ['required', 'string', 'max:50'],
            'season' => ['nullable', 'string', 'max:50'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'is_active' => ['nullable'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        $tournament = Tournament::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'Tournament', $tournament->id, newValue: $tournament->toArray());

        return redirect()->route('admin.sports.index')->with('status', 'प्रतियोगिता थपियो।');
    }

    public function edit(Tournament $sport): View
    {
        return view('admin.sports.form', ['tournament' => $sport]);
    }

    public function update(Request $request, AuditService $audit, Tournament $sport): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sport_type' => ['required', 'string', 'max:50'],
            'season' => ['nullable', 'string', 'max:50'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'is_active' => ['nullable'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $old = $sport->toArray();
        $sport->update($data);
        $audit->record($request->user(), 'UPDATE', 'Tournament', $sport->id, oldValue: $old, newValue: $sport->fresh()->toArray());

        return redirect()->route('admin.sports.index')->with('status', 'प्रतियोगिता अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, Tournament $sport): RedirectResponse
    {
        $old = $sport->toArray();
        $sport->delete();
        $audit->record($request->user(), 'DELETE', 'Tournament', $sport->id, oldValue: $old);

        return redirect()->route('admin.sports.index')->with('status', 'प्रतियोगिता मेटाइयो।');
    }
}