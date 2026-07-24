<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdPosition;
use App\Models\Advertisement;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdvertisementController extends Controller
{
    public function index(): View
    {
        $ads = Advertisement::query()->with('position:id,name,type')->orderByDesc('created_at')->paginate(30);
        $positions = AdPosition::query()->orderBy('name')->get();
        return view('admin.ads.index', compact('ads', 'positions'));
    }

    public function create(): View
    {
        return view('admin.ads.form', [
            'ad' => new Advertisement(),
            'positions' => AdPosition::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateData($request);
        $ad = Advertisement::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'Advertisement', $ad->id, newValue: $ad->toArray());

        return redirect()->route('admin.ads.index')->with('status', 'विज्ञापन थपियो।');
    }

    public function edit(Advertisement $ad): View
    {
        return view('admin.ads.form', [
            'ad' => $ad,
            'positions' => AdPosition::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, AuditService $audit, Advertisement $ad): RedirectResponse
    {
        $data = $this->validateData($request);
        $old = $ad->toArray();
        $ad->update($data);
        $audit->record($request->user(), 'UPDATE', 'Advertisement', $ad->id, oldValue: $old, newValue: $ad->fresh()->toArray());

        return redirect()->route('admin.ads.index')->with('status', 'विज्ञापन अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, Advertisement $ad): RedirectResponse
    {
        $old = $ad->toArray();
        $ad->delete();
        $audit->record($request->user(), 'DELETE', 'Advertisement', $ad->id, oldValue: $old);

        return redirect()->route('admin.ads.index')->with('status', 'विज्ञापन मेटाइयो।');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'target_url' => ['required', 'string', 'max:500'],
            'ad_position_id' => ['required', 'string', 'exists:ad_positions,id'],
            'is_active' => ['nullable'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
            'impressions' => 0,
            'clicks' => 0,
        ];
    }
}