<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Customer;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $type = $request->get('type');
        $country = $request->get('country');

        $sort = in_array($request->get('sort'), [
            'name',
            'type',
            'email',
            'phone',
            'country',
            'created_at'
        ]) ? $request->get('sort') : 'created_at';

        $dir = $request->get('dir') === 'asc' ? 'asc' : 'desc';

        $banks = Bank::query()
            ->when($q, fn($qr) => $qr->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('country', 'like', "%{$q}%")
                    ->orWhere('note', 'like', "%{$q}%")
                    ->orWhere('swift_code', 'like', "%{$q}%")
                    ->orWhere('bank_account', 'like', "%{$q}%");
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

        if (auth()->check()) {
            $data['created_by'] = auth()->id();
        }

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

    /**
     * VALIDATION FIXED
     * - Customer Bank → must exist in customers table
     * - Shipper Bank → must exist in companies table (ANY company, NOT category filtered)
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:Customer Bank,Shipper Bank'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:40'],
            'country' => ['nullable', 'string', 'max:120'],
            'note' => ['nullable', 'string'],
            'swift_code' => ['nullable', 'string', 'max:100'],
            'bank_account' => ['nullable', 'string', 'max:190'],
            'company_id' => ['nullable', 'integer'],
        ]);

        $companyId = $request->input('company_id');

        $data['company_id'] = $companyId;
        $data['company_type'] = null;

        if ($companyId) {

            if ($data['type'] === 'Customer Bank') {
                if (!Customer::whereKey($companyId)->exists()) {
                    throw ValidationException::withMessages([
                        'company_id' => ['Selected customer does not exist.'],
                    ]);
                }
                $data['company_type'] = Customer::class;
            }

            if ($data['type'] === 'Shipper Bank') {
                if (!Company::whereKey($companyId)->exists()) {
                    throw ValidationException::withMessages([
                        'company_id' => ['Selected company does not exist in Companies.'],
                    ]);
                }
                $data['company_type'] = Company::class;
            }
        }

        return $data;
    }

    /**
     * FIXED companyOptions()
     * - Shipper Bank: return only Shipper category IF EXISTS
     * - Otherwise fallback to ALL companies
     */
    public function companyOptions(Request $request)
    {
        $type = $request->get('type'); // 'customer' | 'shipper'
        $q = trim((string) $request->get('q'));

        /** ---------------------- CUSTOMER LIST ---------------------- */
        if ($type === 'customer') {
            $rows = Customer::query()
                ->when($q, fn($qr) => $qr->where('name', 'like', "%{$q}%"))
                ->orderBy('name')
                ->limit(20)
                ->get(['id', 'name']);

            return response()->json(
                $rows->map(fn($row) => [
                    'id' => $row->id,
                    'name' => $row->name,
                    'type' => 'customer',
                ])
            );
        }

        /** ---------------------- SHIPPER LIST ---------------------- */

        if ($type === 'shipper') {

            // 1️⃣ First try ONLY Shipper category
            $shipperCompanies = Company::query()
                ->whereHas(
                    'category',
                    fn($c) =>
                    $c->where('name', 'Shipper')->orWhere('slug', 'shipper')
                )
                ->when(
                    $q,
                    fn($qr) =>
                    $qr->where('company_name', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                )
                ->orderBy('company_name')
                ->limit(20)
                ->get(['id', 'company_name', 'name']);

            // 2️⃣ If none exist, fallback to ALL companies
            if ($shipperCompanies->isEmpty()) {
                $shipperCompanies = Company::query()
                    ->when(
                        $q,
                        fn($qr) =>
                        $qr->where('company_name', 'like', "%{$q}%")
                            ->orWhere('name', 'like', "%{$q}%")
                    )
                    ->orderBy('company_name')
                    ->limit(20)
                    ->get(['id', 'company_name', 'name']);
            }

            return response()->json(
                $shipperCompanies->map(fn($row) => [
                    'id' => $row->id,
                    'name' => $row->company_name ?: $row->name,
                    'type' => 'shipper',
                ])
            );
        }

        return response()->json([]);
    }
}