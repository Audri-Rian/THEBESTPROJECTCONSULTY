<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SuppliersController extends Controller
{
    public function index() {
        $suppliers = Supplier::with('address')->get()->map(function ($supplier) {
            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'cnpj' => $supplier->cnpj,
                'email' => $supplier->email,
                'phone' => $supplier->phone,
                'street' => $supplier->address->street ?? null,
                'city' => $supplier->address->city ?? null,
                'state' => $supplier->address->state ?? null,
                'postal_code' => $supplier->address->postal_code ?? null,
                'district' => $supplier->address->district ?? null,
            ];
        });

        return Inertia::render('SuppliersManager', compact('suppliers'));
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|numeric|min:0',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
        ]);

        $isAddressEmpty = empty($validatedData['street']) &&
            empty($validatedData['city']) &&
            empty($validatedData['state']) &&
            empty($validatedData['postal']) &&
            empty($validatedData['district']);

        $addressId = null;

        if (!$isAddressEmpty) {
            $address = Address::create([
                'street' => $validatedData['street'],
                'city' => $validatedData['city'],
                'state' => $validatedData['state'],
                'postal_code' => $validatedData['postal_code'],
                'district' => $validatedData['district'],
            ]);

            $addressId = $address->id;
        }

        Supplier::create([
            'name' => $validatedData['name'],
            'cnpj' => $validatedData['cnpj'] ?? null,
            'email' => $validatedData['email'] ?? null,
            'phone' => $validatedData['phone'] ?? null,
            'address_id' => $addressId,
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully!');
    }

    public function update(Request $request, Supplier $supplier) {
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|numeric|min:0',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
        ]);

        $supplier->update([
            'name' => $validatedData['name'],
            'cnpj' => $validatedData['cnpj'] ?? null,
            'email' => $validatedData['email'] ?? null,
            'phone' => $validatedData['phone'] ?? null,
        ]);

        if ($supplier->address) {
            $supplier->address->update([
                'street' => $validatedData['street'] ?? $supplier->address->street,
                'city' => $validatedData['city'] ?? $supplier->address->city,
                'state' => $validatedData['state'] ?? $supplier->address->state,
                'postal_code' => $validatedData['postal_code'] ?? $supplier->address->postal_code,
                'district' => $validatedData['district'] ?? $supplier->address->district,
            ]);
        } else {
            $isAddressEmpty = empty($validatedData['street']) &&
                empty($validatedData['city']) &&
                empty($validatedData['state']) &&
                empty($validatedData['postal_code']) &&
                empty($validatedData['district']);

            if (!$isAddressEmpty) {
                $address = Address::create([
                    'street' => $validatedData['street'] ?? null,
                    'city' => $validatedData['city'] ?? null,
                    'state' => $validatedData['state'] ?? null,
                    'postal_code' => $validatedData['postal_code'] ?? null,
                    'district' => $validatedData['district'] ?? null,
                ]);

                $supplier->update(['address_id' => $address->id]);
            }
        }

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully!');
    }
}
