<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $type = $request->get('type');    // 'Customer Bank' | 'Shipper Bank'
        $country = $request->get('country'); // free text filter

        $sort = in_array($request->get('sort'), ['name', 'type', 'email', 'phone', 'country', 'created_at'])
            ? $request->get('sort') : 'created_at';
        $dir = $request->get('dir') === 'asc' ? 'asc' : 'desc';

        $banks = Bank::query()
            ->when($q, fn($qr) => $qr->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('country', 'like', "%{$q}%")
                    ->orWhere('note', 'like', "%{$q}%");
            }))
            ->when(in_array($type, ['Customer Bank', 'Shipper Bank']), fn($qr) => $qr->where('type', $type))
            ->when($country, fn($qr) => $qr->where('country', 'like', "%{$country}%"))
            ->orderBy($sort, $dir)
            ->paginate(12)
            ->withQueryString();

        return view('banks.index', compact('banks'));
    }

    public function create()
    {
        return view('banks.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if (auth()->check())
            $data['created_by'] = auth()->id();

        $bank = Bank::create($data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Bank created successfully.',
                'id' => $bank->id,
            ]);
        }

        return redirect()->route('admin.banks.index')->with('success', 'Bank created.');
    }

    public function show(Bank $bank)
    {
        return view('banks.show', compact('bank'));
    }

    public function edit(Bank $bank)
    {
        return view('banks.edit', compact('bank'));
    }

    public function update(Request $request, Bank $bank)
    {
        $data = $this->validated($request);
        $bank->update($data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Bank updated successfully.',
                'id' => $bank->id,
            ]);
        }

        return redirect()->route('admin.banks.index')->with('success', 'Bank updated.');
    }

    public function destroy(Request $request, Bank $bank)
    {
        $bank->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => 'Bank deleted successfully.']);
        }

        return redirect()->route('admin.banks.index')->with('success', 'Bank deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:Customer Bank,Shipper Bank'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:40'],
            'country' => ['nullable', 'string', 'max:120'],
            'note' => ['nullable', 'string'],
        ]);
    }
}