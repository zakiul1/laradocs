<?php

namespace App\Http\Controllers;

use App\Models\Shipper;
use Illuminate\Http\Request;

class ShipperController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $sort = in_array($request->get('sort'), ['name', 'email', 'phone', 'website', 'created_at'])
            ? $request->get('sort') : 'created_at';
        $dir = $request->get('dir') === 'asc' ? 'asc' : 'desc';

        $shippers = Shipper::query()
            ->when($q, fn($qr) => $qr->where(function ($s) use ($q) {
                $s->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('website', 'like', "%{$q}%");
            }))
            ->orderBy($sort, $dir)
            ->paginate(12)
            ->withQueryString();

        return view('shippers.index', compact('shippers'));
    }

    public function create()
    {
        return view('shippers.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if (auth()->check()) {
            $data['created_by'] = auth()->id();
        }

        $shipper = Shipper::create($data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Shipper created successfully.',
                'id' => $shipper->id,
            ]);
        }

        return redirect()->route('admin.shippers.index')->with('success', 'Shipper created.');
    }

    public function show(Shipper $shipper)
    {
        return view('shippers.show', compact('shipper'));
    }

    public function edit(Shipper $shipper)
    {
        return view('shippers.edit', compact('shipper'));
    }

    public function update(Request $request, Shipper $shipper)
    {
        $data = $this->validated($request);
        $shipper->update($data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Shipper updated successfully.',
                'id' => $shipper->id,
            ]);
        }

        return redirect()->route('admin.shippers.index')->with('success', 'Shipper updated.');
    }

    public function destroy(Request $request, Shipper $shipper)
    {
        $shipper->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => 'Shipper deleted successfully.']);
        }

        return redirect()->route('admin.shippers.index')->with('success', 'Shipper deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'url', 'max:255'],
        ]);
    }
}