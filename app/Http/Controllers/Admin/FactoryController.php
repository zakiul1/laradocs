<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Factory;
use App\Models\FactoryDocument;
use App\Models\FactoryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FactoryController extends Controller
{
    public function index()
    {
        $factories = Factory::with(['categories', 'creator'])->latest()->paginate(12);
        return view('admin.factories.index', compact('factories'));
    }

    public function create()
    {
        return $this->formView(new Factory());
    }

    public function edit(Factory $factory)
    {
        $factory->load(['photos', 'documents', 'categories']);
        return $this->formView($factory);
    }

    /**
     * Shared form view.
     * Sends a flat array of factory-scope categories: [{id, name, parent_id}]
     */
    protected function formView(Factory $factory)
    {
        // Flat list (scope=factory)
        // Flat list (scope=factory)
        $categories = Category::query()
            ->where('scope', 'factory')
            ->select('id', 'name', 'parent_id')
            ->orderBy('name')
            ->get()
            ->map(fn($c) => [
                'id' => (int) $c->id,
                'name' => $c->name,
                'parent_id' => is_null($c->parent_id) ? null : (int) $c->parent_id,
            ])
            ->toArray();

        //dd($categories);



        // Selected category ids for edit
        $selected = $factory->exists
            ? $factory->categories()->pluck('categories.id')->map(fn($id) => (int) $id)->toArray()
            : [];


        return view('admin.factories.form', [
            'factory' => $factory,
            'categories' => $categories,   // flat array for Alpine
            'selected' => $selected,      // [] on create; ids on edit
            'isEdit' => $factory->exists,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data) {
            $data['created_by'] = $request->user()->id;
            $data['updated_by'] = $request->user()->id;

            $factory = Factory::create($data);

            $this->handleUploads($request, $factory);

            // sync categories (filtered to factory scope)
            $catIds = $this->filterFactoryScopeCategoryIds($request->input('category_ids', []));
            $factory->syncCategories($catIds);
        });

        return redirect()->route('admin.factories.index')->with('status', 'Factory created.');
    }

    public function update(Request $request, Factory $factory)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $factory, $data) {
            $data['updated_by'] = $request->user()->id;
            $factory->update($data);

            // removals
            $removePhotoIds = (array) $request->input('remove_photos', []);
            if (!empty($removePhotoIds)) {
                foreach ($factory->photos()->whereIn('id', $removePhotoIds)->get() as $p) {
                    \Storage::disk('public')->delete($p->path);
                    $p->delete();
                }
            }

            $removeDocIds = (array) $request->input('remove_docs', []);
            if (!empty($removeDocIds)) {
                foreach ($factory->documents()->whereIn('id', $removeDocIds)->get() as $d) {
                    \Storage::disk('public')->delete($d->path);
                    $d->delete();
                }
            }

            $this->handleUploads($request, $factory);

            // sync categories (filtered to factory scope)
            $catIds = $this->filterFactoryScopeCategoryIds($request->input('category_ids', []));
            $factory->syncCategories($catIds);
        });

        return back()->with('status', 'Factory updated.');
    }

    public function destroy(Factory $factory)
    {
        DB::transaction(function () use ($factory) {
            foreach ($factory->photos as $p) {
                \Storage::disk('public')->delete($p->path);
            }
            foreach ($factory->documents as $d) {
                \Storage::disk('public')->delete($d->path);
            }
            $factory->categories()->detach();
            $factory->delete();
        });

        return redirect()->route('admin.factories.index')->with('status', 'Factory deleted.');
    }

    /**
     * Validation rules
     */
    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/', 'max:20'],
            'lines' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:2000'],

            // categories
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],

            // uploads
            'photos' => ['nullable', 'array', 'max:30'],
            'photos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'documents' => ['nullable', 'array', 'max:30'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:20480'],
        ], [
            'phone.regex' => 'Use Bangladeshi format, e.g. 01XXXXXXXXX or +8801XXXXXXXXX.',
        ]);
    }

    /**
     * Handle file uploads (photos & documents)
     */
    protected function handleUploads(Request $request, Factory $factory): void
    {
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $path = $file->store("factories/{$factory->id}/photos", 'public');
                FactoryPhoto::create([
                    'factory_id' => $factory->id,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store("factories/{$factory->id}/docs", 'public');
                FactoryDocument::create([
                    'factory_id' => $factory->id,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ]);
            }
        }
    }

    /**
     * Keep only category IDs that belong to scope = 'factory'
     */
    protected function filterFactoryScopeCategoryIds(array $ids): array
    {
        if (empty($ids))
            return [];
        return Category::query()
            ->whereIn('id', $ids)
            ->where('scope', 'factory')
            ->pluck('id')
            ->all();
    }
}