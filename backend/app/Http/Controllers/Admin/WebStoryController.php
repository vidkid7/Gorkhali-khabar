<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\WebStory;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WebStoryController extends Controller
{
    public function index(): View
    {
        $stories = WebStory::query()
            ->with('category:id,name')
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.web-stories.index', compact('stories'));
    }

    public function create(): View
    {
        return view('admin.web-stories.form', [
            'story' => new WebStory(),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active');
        $story = WebStory::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'WebStory', $story->id, newValue: $story->toArray());

        return redirect()->route('admin.web-stories.index')->with('status', 'वेब स्टोरी थपियो।');
    }

    public function edit(WebStory $webStory): View
    {
        return view('admin.web-stories.form', [
            'story' => $webStory,
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, AuditService $audit, WebStory $webStory): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active');
        $old = $webStory->toArray();
        $webStory->update($data);
        $audit->record($request->user(), 'UPDATE', 'WebStory', $webStory->id, oldValue: $old, newValue: $webStory->fresh()->toArray());

        return redirect()->route('admin.web-stories.index')->with('status', 'वेब स्टोरी अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, WebStory $webStory): RedirectResponse
    {
        $old = $webStory->toArray();
        $webStory->delete();
        $audit->record($request->user(), 'DELETE', 'WebStory', $webStory->id, oldValue: $old);

        return redirect()->route('admin.web-stories.index')->with('status', 'वेब स्टोरी मेटाइयो।');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'string', 'exists:categories,id'],
            'cover_image' => ['nullable', 'string', 'max:500'],
            'slides' => ['nullable'],
            'is_active' => ['nullable'],
        ]);
    }
}