<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\Address;

class AddressController extends Controller
{
    public function edit(Item $item) {
        $deliveryAddress = auth()->user()->address;

        return view('addresses.edit', compact('item', 'deliveryAddress'));
    }

    public function update(AddressRequest $request, Item $item) {
        $user = auth()->user();
        $user->address->update([
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        return redirect()->route('purchases.create', ['item' => $item->id]);
    }
}
