<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PanchangData;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PanchangController extends Controller
{
    public function index(Request $request): View
    {
        $query = PanchangData::query();
        if ($year = $request->query('year')) {
            $query->whereYear('ad_date', (int) $year);
        }
        $entries = $query->orderByDesc('ad_date')->paginate(60)->withQueryString();

        return view('admin.panchang.index', compact('entries'));
    }

    public function create(): View
    {
        return view('admin.panchang.form', ['entry' => new PanchangData()]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateData($request);
        $entry = PanchangData::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'PanchangData', $entry->id, newValue: $entry->toArray());

        return redirect()->route('admin.panchang.index')->with('status', 'पञ्चाङ्ग थपियो।');
    }

    public function edit(PanchangData $panchang): View
    {
        return view('admin.panchang.form', ['entry' => $panchang]);
    }

    public function update(Request $request, AuditService $audit, PanchangData $panchang): RedirectResponse
    {
        $data = $this->validateData($request);
        $old = $panchang->toArray();
        $panchang->update($data);
        $audit->record($request->user(), 'UPDATE', 'PanchangData', $panchang->id, oldValue: $old, newValue: $panchang->fresh()->toArray());

        return redirect()->route('admin.panchang.index')->with('status', 'पञ्चाङ्ग अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, PanchangData $panchang): RedirectResponse
    {
        $old = $panchang->toArray();
        $panchang->delete();
        $audit->record($request->user(), 'DELETE', 'PanchangData', $panchang->id, oldValue: $old);

        return redirect()->route('admin.panchang.index')->with('status', 'पञ्चाङ्ग मेटाइयो।');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'ad_date' => ['required', 'date'],
            'bs_date' => ['nullable', 'string', 'max:20'],
            'tithi' => ['nullable', 'string', 'max:80'],
            'nakshatra' => ['nullable', 'string', 'max:80'],
            'yoga' => ['nullable', 'string', 'max:80'],
            'karana' => ['nullable', 'string', 'max:80'],
            'sunrise' => ['nullable', 'string', 'max:10'],
            'sunset' => ['nullable', 'string', 'max:10'],
            'moonrise' => ['nullable', 'string', 'max:10'],
            'moonset' => ['nullable', 'string', 'max:10'],
            'festivals' => ['nullable', 'string', 'max:500'],
        ]);
    }
}