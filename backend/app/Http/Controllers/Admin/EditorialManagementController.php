<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContentPage;
use App\Models\EditorialMenu;
use App\Models\HomepageSection;
use App\Models\LiveBlog;
use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EditorialManagementController extends Controller
{
    public function index(Request $request): View
    {
        $config = $this->config($request);
        $items = $config['model']::query()
            ->orderBy($config['orderBy'][0], $config['orderBy'][1])
            ->paginate(25);

        return view('admin.editorial.index', compact('config', 'items'));
    }

    public function create(Request $request): View
    {
        $config = $this->config($request);
        $item = new $config['model']();

        return view('admin.editorial.form', compact('config', 'item'));
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $config = $this->config($request);
        $data = $this->validatedData($request, $config);
        $item = $config['model']::query()->create($data);
        $audit->record($request->user(), 'CREATE', class_basename($item), $item->id, newValue: $item->toArray());

        return redirect()->route("admin.{$config['resource']}.edit", $item)
            ->with('status', "{$config['label']} सिर्जना गरियो।");
    }

    public function edit(Request $request, string $item): View
    {
        $config = $this->config($request);
        $item = $config['model']::query()->findOrFail($item);
        if ($item instanceof LiveBlog) {
            $item->load('posts');
        }

        return view('admin.editorial.form', compact('config', 'item'));
    }

    public function update(Request $request, AuditService $audit, string $item): RedirectResponse
    {
        $config = $this->config($request);
        $item = $config['model']::query()->findOrFail($item);
        $old = $item->toArray();
        $item->update($this->validatedData($request, $config, $item));
        $audit->record($request->user(), 'UPDATE', class_basename($item), $item->id, oldValue: $old, newValue: $item->fresh()->toArray());

        return redirect()->route("admin.{$config['resource']}.edit", $item)
            ->with('status', "{$config['label']} अपडेट गरियो।");
    }

    public function destroy(Request $request, AuditService $audit, string $item): RedirectResponse
    {
        $config = $this->config($request);
        $item = $config['model']::query()->findOrFail($item);
        $old = $item->toArray();
        $item->delete();
        $audit->record($request->user(), 'DELETE', class_basename($item), $item->id, oldValue: $old);

        return redirect()->route("admin.{$config['resource']}.index")
            ->with('status', "{$config['label']} मेटाइयो।");
    }

    private function validatedData(Request $request, array $config, ?Model $item = null): array
    {
        $rules = [];
        foreach ($config['fields'] as $name => $field) {
            $rules[$name] = $field['rules'];
            if (($field['unique'] ?? false) === true) {
                $rules[$name][] = Rule::unique($item?->getTable() ?? (new $config['model']())->getTable(), $name)
                    ->ignore($item?->getKey());
            }
        }

        $data = $request->validate($rules);
        foreach ($config['fields'] as $name => $field) {
            if (($field['type'] ?? null) === 'checkbox') {
                $data[$name] = $request->boolean($name);
            }
        }

        return $data;
    }

    private function config(Request $request): array
    {
        $resource = (string) $request->route('editorialResource');
        $categories = Category::query()->where('is_active', true)->orderBy('sort_order')->pluck('name', 'slug')->all();
        $menus = EditorialMenu::query()->orderBy('location')->orderBy('sort_order')->pluck('label', 'id')->all();

        $configs = [
            'pages' => [
                'model' => ContentPage::class,
                'label' => 'पृष्ठ',
                'heading' => 'सामग्री पृष्ठहरू',
                'orderBy' => ['title', 'asc'],
                'columns' => ['title', 'slug', 'is_published'],
                'fields' => [
                    'title' => ['label' => 'शीर्षक', 'rules' => ['required', 'string', 'max:255']],
                    'title_en' => ['label' => 'Title (English)', 'rules' => ['nullable', 'string', 'max:255']],
                    'slug' => ['label' => 'Slug', 'rules' => ['required', 'alpha_dash', 'max:255'], 'unique' => true],
                    'body' => ['label' => 'सामग्री', 'type' => 'textarea', 'rules' => ['required', 'string']],
                    'body_en' => ['label' => 'Content (English)', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    'is_published' => ['label' => 'प्रकाशित', 'type' => 'checkbox', 'rules' => ['nullable', 'boolean']],
                ],
            ],
            'menus' => [
                'model' => EditorialMenu::class,
                'label' => 'मेनु',
                'heading' => 'सम्पादकीय मेनु',
                'orderBy' => ['sort_order', 'asc'],
                'columns' => ['label', 'location', 'href', 'sort_order', 'is_active'],
                'fields' => [
                    'location' => ['label' => 'स्थान', 'type' => 'select', 'options' => ['header' => 'Header', 'footer' => 'Footer', 'mobile' => 'Mobile'], 'rules' => ['required', Rule::in(['header', 'footer', 'mobile'])]],
                    'label' => ['label' => 'नाम', 'rules' => ['required', 'string', 'max:255']],
                    'label_en' => ['label' => 'Label (English)', 'rules' => ['nullable', 'string', 'max:255']],
                    'href' => ['label' => 'Link', 'rules' => ['required', 'string', 'max:500']],
                    'parent_id' => ['label' => 'Parent Menu', 'type' => 'select', 'options' => ['' => '— None —'] + $menus, 'rules' => ['nullable', 'string', 'exists:menus,id']],
                    'sort_order' => ['label' => 'क्रम', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                    'is_active' => ['label' => 'सक्रिय', 'type' => 'checkbox', 'rules' => ['nullable', 'boolean']],
                ],
            ],
            'homepage-sections' => [
                'model' => HomepageSection::class,
                'label' => 'गृहपृष्ठ खण्ड',
                'heading' => 'गृहपृष्ठ खण्डहरू',
                'orderBy' => ['sort_order', 'asc'],
                'columns' => ['title', 'section_key', 'category_slug', 'layout', 'sort_order', 'is_active'],
                'fields' => [
                    'section_key' => ['label' => 'Section Key', 'rules' => ['required', 'alpha_dash', 'max:255'], 'unique' => true],
                    'title' => ['label' => 'शीर्षक', 'rules' => ['required', 'string', 'max:255']],
                    'title_en' => ['label' => 'Title (English)', 'rules' => ['nullable', 'string', 'max:255']],
                    'category_slug' => ['label' => 'वर्ग', 'type' => 'select', 'options' => ['' => '— None —'] + $categories, 'rules' => ['nullable', 'string', 'exists:categories,slug']],
                    'layout' => ['label' => 'Layout', 'type' => 'select', 'options' => ['featured' => 'Featured', 'grid' => 'Grid', 'list' => 'List'], 'rules' => ['required', Rule::in(['featured', 'grid', 'list'])]],
                    'sort_order' => ['label' => 'क्रम', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                    'is_active' => ['label' => 'सक्रिय', 'type' => 'checkbox', 'rules' => ['nullable', 'boolean']],
                ],
            ],
            'live-blogs' => [
                'model' => LiveBlog::class,
                'label' => 'लाइभ ब्लग',
                'heading' => 'लाइभ ब्लगहरू',
                'orderBy' => ['created_at', 'desc'],
                'columns' => ['title', 'slug', 'status', 'started_at', 'ended_at'],
                'fields' => [
                    'title' => ['label' => 'शीर्षक', 'rules' => ['required', 'string', 'max:255']],
                    'title_en' => ['label' => 'Title (English)', 'rules' => ['nullable', 'string', 'max:255']],
                    'slug' => ['label' => 'Slug', 'rules' => ['required', 'alpha_dash', 'max:255'], 'unique' => true],
                    'summary' => ['label' => 'सारांश', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    'summary_en' => ['label' => 'Summary (English)', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
                    'status' => ['label' => 'स्थिति', 'type' => 'select', 'options' => ['DRAFT' => 'Draft', 'LIVE' => 'Live', 'ENDED' => 'Ended'], 'rules' => ['required', Rule::in(['DRAFT', 'LIVE', 'ENDED'])]],
                    'started_at' => ['label' => 'सुरु समय', 'type' => 'datetime-local', 'rules' => ['nullable', 'date']],
                    'ended_at' => ['label' => 'अन्त्य समय', 'type' => 'datetime-local', 'rules' => ['nullable', 'date', 'after_or_equal:started_at']],
                ],
            ],
        ];

        abort_unless(isset($configs[$resource]), 404);

        return ['resource' => $resource] + $configs[$resource];
    }
}
