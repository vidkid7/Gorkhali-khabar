<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuickLink;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuickLinkController extends Controller
{
    public function index(): View
    {
        $links = QuickLink::query()->orderBy('sort_order')->orderBy('title')->paginate(50);
        return view('admin.quick-links.index', compact('links'));
    }

    public function create(): View
    {
        return view('admin.quick-links.form', ['link' => new QuickLink()]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateData($request);
        $link = QuickLink::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'QuickLink', $link->id, newValue: $link->toArray());

        return redirect()->route('admin.quick-links.index')->with('status', 'द्रुत लिंक थपियो।');
    }

    public function edit(QuickLink $quickLink): View
    {
        return view('admin.quick-links.form', ['link' => $quickLink]);
    }

    public function update(Request $request, AuditService $audit, QuickLink $quickLink): RedirectResponse
    {
        $data = $this->validateData($request);
        $old = $quickLink->toArray();
        $quickLink->update($data);
        $audit->record($request->user(), 'UPDATE', 'QuickLink', $quickLink->id, oldValue: $old, newValue: $quickLink->fresh()->toArray());

        return redirect()->route('admin.quick-links.index')->with('status', 'द्रुत लिंक अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, QuickLink $quickLink): RedirectResponse
    {
        $old = $quickLink->toArray();
        $quickLink->delete();
        $audit->record($request->user(), 'DELETE', 'QuickLink', $quickLink->id, oldValue: $old);

        return redirect()->route('admin.quick-links.index')->with('status', 'द्रुत लिंक मेटाइयो।');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'title_en' => ['nullable', 'string', 'max:120'],
            'url' => ['required', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}