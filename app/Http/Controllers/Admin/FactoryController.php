<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Factory;
use App\Models\FactoryDocument;
use App\Models\FactoryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

    protected function formView(Factory $factory)
    {
        // Only factory-scope categories
        $categories = Category::scope('factory')
            ->with('children:id,parent_id,name')
            ->orderBy('position')
            ->get();

        // Selected category ids for edit
        $selected = $factory->exists ? $factory->categories()->pluck('categories.id')->toArray() : [];

        return view('admin.factories.form', [
            'factory' => $factory,
            'categories' => $categories,
            'selected' => $selected,
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

            // sync categories (multi)
            $catIds = $request->input('category_ids', []);
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

            // handle removals + uploads
            if ($request->filled('remove_photos')) {
                foreach ($factory->photos()->whereIn('id', $request->remove_photos)->get() as $p) {
                    \Storage::disk('public')->delete($p->path);
                    $p->delete();
                }
            }
            if ($request->filled('remove_docs')) {
                foreach ($factory->documents()->whereIn('id', $request->remove_docs)->get() as $d) {
                    \Storage::disk('public')->delete($d->path);
                    $d->delete();
                }
            }
            $this->handleUploads($request, $factory);

            // sync categories (multi)
            $catIds = $request->input('category_ids', []);
            $factory->syncCategories($catIds);
        });

        return back()->with('status', 'Factory updated.');
    }

    public function destroy(Factory $factory)
    {
        DB::transaction(function () use ($factory) {
            foreach ($factory->photos as $p)
                \Storage::disk('public')->delete($p->path);
            foreach ($factory->documents as $d)
                \Storage::disk('public')->delete($d->path);
            $factory->categories()->detach();
            $factory->delete();
        });

        return redirect()->route('admin.factories.index')->with('status', 'Factory deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/', 'max:20'],
            'lines' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:2000'],

            // multi-categories
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
}