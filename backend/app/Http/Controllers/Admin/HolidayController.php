<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HolidayController extends Controller
{
    public function index(): View
    {
        $holidays = Holiday::query()->orderBy('date')->paginate(50);
        return view('admin.holidays.index', compact('holidays'));
    }

    public function create(): View
    {
        return view('admin.holidays.form', ['holiday' => new Holiday()]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateData($request);
        $holiday = Holiday::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'Holiday', $holiday->id, newValue: $holiday->toArray());

        return redirect()->route('admin.holidays.index')->with('status', 'बिदा थपियो।');
    }

    public function edit(Holiday $holiday): View
    {
        return view('admin.holidays.form', compact('holiday'));
    }

    public function update(Request $request, AuditService $audit, Holiday $holiday): RedirectResponse
    {
        $data = $this->validateData($request);
        $old = $holiday->toArray();
        $holiday->update($data);
        $audit->record($request->user(), 'UPDATE', 'Holiday', $holiday->id, oldValue: $old, newValue: $holiday->fresh()->toArray());

        return redirect()->route('admin.holidays.index')->with('status', 'बिदा अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, Holiday $holiday): RedirectResponse
    {
        $old = $holiday->toArray();
        $holiday->delete();
        $audit->record($request->user(), 'DELETE', 'Holiday', $holiday->id, oldValue: $old);

        return redirect()->route('admin.holidays.index')->with('status', 'बिदा मेटाइयो।');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'name_en' => ['nullable', 'string', 'max:120'],
            'date' => ['required', 'date'],
            'type' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_public_holiday' => ['nullable'],
        ]) + ['is_public_holiday' => $request->boolean('is_public_holiday')];
    }
}