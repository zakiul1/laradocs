<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\FactoryCategory;
use App\Models\FactorySubcategory;
use App\Models\FactoryPhoto;
use App\Models\FactoryDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FactoryController extends Controller
{
    public function index(Request $request)
    {
        $q = Factory::with(['category', 'subcategory'])->orderByDesc('id');

        if ($request->filled('category_id'))
            $q->where('category_id', $request->integer('category_id'));
        if ($request->filled('subcategory_id'))
            $q->where('subcategory_id', $request->integer('subcategory_id'));

        $factories = $q->paginate(20)->withQueryString();
        $categories = FactoryCategory::forSelect();
        $subcategories = $request->filled('category_id') ? FactorySubcategory::forSelectByCategory((int) $request->category_id) : [];

        return view('admin.factories.index', compact('factories', 'categories', 'subcategories'));
    }

    public function create()
    {
        $categories = FactoryCategory::forSelect();
        return view('admin.factories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'registration_no' => ['nullable', 'string', 'max:255'],
            'total_employees' => ['nullable', 'integer', 'min:0'],
            'lines' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:factory_categories,id'],
            'subcategory_id' => ['nullable', 'exists:factory_subcategories,id'],

            'photos.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5MB each
            'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx,zip', 'max:10240'], // 10MB each
        ]);

        // ensure subcategory belongs to category
        if (!empty($data['subcategory_id']) && !empty($data['category_id'])) {
            $ok = FactorySubcategory::where('id', $data['subcategory_id'])
                ->where('factory_category_id', $data['category_id'])->exists();
            if (!$ok)
                return back()->withErrors(['subcategory_id' => 'Selected subcategory does not belong to chosen category.'])->withInput();
        }

        DB::transaction(function () use ($request, $data) {
            $data['created_by'] = $request->user()?->id;
            $data['updated_by'] = $request->user()?->id;
            $data['lines'] = $data['lines'] ?? 0;
            $data['total_employees'] = $data['total_employees'] ?? 0;

            /** @var Factory $factory */
            $factory = Factory::create($data);

            // Photos
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $file) {
                    if (!$file)
                        continue;
                    $path = $file->store("factories/{$factory->id}/photos", 'public');
                    $factory->photos()->create([
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                    ]);
                }
            }

            // Documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    if (!$file)
                        continue;
                    $path = $file->store("factories/{$factory->id}/documents", 'public');
                    $factory->documents()->create([
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ]);
                }
            }
        });

        return $request->wantsJson()
            ? response()->json(['success' => true, 'redirect' => route('admin.factories.index')])
            : redirect()->route('admin.factories.index')->with('success', 'Factory created.');
    }

    public function edit(Factory $factory)
    {
        $factory->load(['category', 'subcategory', 'photos', 'documents']);
        $categories = FactoryCategory::forSelect();
        return view('admin.factories.edit', compact('factory', 'categories'));
    }

    public function update(Request $request, Factory $factory)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'registration_no' => ['nullable', 'string', 'max:255'],
            'total_employees' => ['nullable', 'integer', 'min:0'],
            'lines' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:factory_categories,id'],
            'subcategory_id' => ['nullable', 'exists:factory_subcategories,id'],

            'photos.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx,zip', 'max:10240'],

            'remove_photo_ids' => ['nullable', 'array'],
            'remove_photo_ids.*' => ['integer', 'exists:factory_photos,id'],
            'remove_document_ids' => ['nullable', 'array'],
            'remove_document_ids.*' => ['integer', 'exists:factory_documents,id'],
        ]);

        if (!empty($data['subcategory_id']) && !empty($data['category_id'])) {
            $ok = FactorySubcategory::where('id', $data['subcategory_id'])
                ->where('factory_category_id', $data['category_id'])->exists();
            if (!$ok)
                return back()->withErrors(['subcategory_id' => 'Selected subcategory does not belong to chosen category.'])->withInput();
        }

        DB::transaction(function () use ($request, $factory, $data) {
            $data['updated_by'] = $request->user()?->id;
            $data['lines'] = $data['lines'] ?? 0;
            $data['total_employees'] = $data['total_employees'] ?? 0;

            $factory->update($data);

            // Remove selected existing files
            foreach ((array) ($request->input('remove_photo_ids', [])) as $pid) {
                $photo = $factory->photos()->find($pid);
                if ($photo) {
                    Storage::disk('public')->delete($photo->path);
                    $photo->delete();
                }
            }
            foreach ((array) ($request->input('remove_document_ids', [])) as $did) {
                $doc = $factory->documents()->find($did);
                if ($doc) {
                    Storage::disk('public')->delete($doc->path);
                    $doc->delete();
                }
            }

            // New uploads
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $file) {
                    if (!$file)
                        continue;
                    $path = $file->store("factories/{$factory->id}/photos", 'public');
                    $factory->photos()->create([
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                    ]);
                }
            }
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    if (!$file)
                        continue;
                    $path = $file->store("factories/{$factory->id}/documents", 'public');
                    $factory->documents()->create([
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ]);
                }
            }
        });

        return $request->wantsJson()
            ? response()->json(['success' => true, 'redirect' => route('admin.factories.edit', $factory)])
            : redirect()->route('admin.factories.edit', $factory)->with('success', 'Factory updated.');
    }

    public function destroy(Factory $factory)
    {
        // Storage deletes are handled by cascade + explicit deletes if needed
        foreach ($factory->photos as $p)
            Storage::disk('public')->delete($p->path);
        foreach ($factory->documents as $d)
            Storage::disk('public')->delete($d->path);
        $factory->delete();

        return redirect()->route('admin.factories.index')->with('success', 'Factory deleted.');
    }

    // Dependent subcategories
    public function subcategoriesJson(Request $request)
    {
        $catId = (int) $request->query('category_id');
        $items = FactorySubcategory::where('factory_category_id', $catId)
            ->orderBy('position')->orderBy('name')->get(['id', 'name']);
        return response()->json($items);
    }

    // Quick create from form
    public function quickCreateCategory(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);
        $c = FactoryCategory::create([
            'name' => $data['name'],
            'position' => 0,
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);
        return response()->json(['id' => $c->id, 'name' => $c->name]);
    }

    public function quickCreateSubcategory(Request $request)
    {
        $data = $request->validate([
            'factory_category_id' => ['required', 'exists:factory_categories,id'],
            'name' => ['required', 'string', 'max:255']
        ]);
        $s = FactorySubcategory::create([
            'factory_category_id' => $data['factory_category_id'],
            'name' => $data['name'],
            'position' => 0,
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);
        return response()->json(['id' => $s->id, 'name' => $s->name]);
    }
}