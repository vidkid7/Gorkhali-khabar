<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rashifal;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RashifalController extends Controller
{
    private const SIGNS = [
        'मेष', 'वृष', 'मिथुन', 'कर्कट', 'सिंह', 'कन्या',
        'तुला', 'वृश्चिक', 'धनु', 'मकर', 'कुम्भ', 'मीन',
    ];

    public function index(): View
    {
        $items = Rashifal::query()
            ->orderByDesc('date')
            ->paginate(50);

        return view('admin.rashifal.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.rashifal.form', ['item' => new Rashifal(), 'signs' => self::SIGNS]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateData($request);
        $item = Rashifal::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'Rashifal', $item->id, newValue: $item->toArray());

        return redirect()->route('admin.rashifal.index')->with('status', 'राशिफल थपियो।');
    }

    public function edit(Rashifal $rashifal): View
    {
        return view('admin.rashifal.form', ['item' => $rashifal, 'signs' => self::SIGNS]);
    }

    public function update(Request $request, AuditService $audit, Rashifal $rashifal): RedirectResponse
    {
        $data = $this->validateData($request);
        $old = $rashifal->toArray();
        $rashifal->update($data);
        $audit->record($request->user(), 'UPDATE', 'Rashifal', $rashifal->id, oldValue: $old, newValue: $rashifal->fresh()->toArray());

        return redirect()->route('admin.rashifal.index')->with('status', 'राशिफल अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, Rashifal $rashifal): RedirectResponse
    {
        $old = $rashifal->toArray();
        $rashifal->delete();
        $audit->record($request->user(), 'DELETE', 'Rashifal', $rashifal->id, oldValue: $old);

        return redirect()->route('admin.rashifal.index')->with('status', 'राशिफल मेटाइयो।');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'sign' => ['required', 'string', 'max:30'],
            'date' => ['required', 'date'],
            'prediction' => ['required', 'string'],
            'prediction_en' => ['nullable', 'string'],
            'lucky_number' => ['nullable', 'string', 'max:20'],
            'lucky_color' => ['nullable', 'string', 'max:30'],
        ]);
    }
}