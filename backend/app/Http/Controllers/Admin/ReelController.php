<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReelController extends Controller
{
    public function index(): View
    {
        $reels = Reel::query()->orderByDesc('created_at')->paginate(30);
        return view('admin.reels.index', compact('reels'));
    }

    public function create(): View
    {
        return view('admin.reels.form', ['reel' => new Reel()]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateData($request);
        $reel = Reel::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'Reel', $reel->id, newValue: $reel->toArray());

        return redirect()->route('admin.reels.index')->with('status', 'रिल सिर्जना गरियो।');
    }

    public function edit(Reel $reel): View
    {
        return view('admin.reels.form', compact('reel'));
    }

    public function update(Request $request, AuditService $audit, Reel $reel): RedirectResponse
    {
        $data = $this->validateData($request);
        $old = $reel->toArray();
        $reel->update($data);
        $audit->record($request->user(), 'UPDATE', 'Reel', $reel->id, oldValue: $old, newValue: $reel->fresh()->toArray());

        return redirect()->route('admin.reels.index')->with('status', 'रिल अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, Reel $reel): RedirectResponse
    {
        $old = $reel->toArray();
        $reel->delete();
        $audit->record($request->user(), 'DELETE', 'Reel', $reel->id, oldValue: $old);

        return redirect()->route('admin.reels.index')->with('status', 'रिल मेटाइयो।');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'video_url' => ['required', 'string', 'max:500'],
            'thumbnail_url' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000'],
            'duration' => ['nullable', 'integer'],
            'is_published' => ['nullable'],
        ]) + [
            'is_published' => $request->boolean('is_published'),
            'view_count' => 0,
        ];
    }
}