<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Get all user addresses
     */
    public function index()
    {
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $addresses
        ]);
    }

    /**
     * Store a new address
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'country' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:30',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'address' => 'required|string',
            'is_default' => 'boolean',
        ]);

        $data['user_id'] = Auth::id();

        // If this is set as default, unset all other default addresses
        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        $address = Address::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Address created successfully',
            'data' => $address
        ], 201);
    }

    /**
     * Get a specific address
     */
    public function show($id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $address
        ]);
    }

    /**
     * Update an address
     */
    public function update(Request $request, $id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);

        $data = $request->validate([
            'country' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:30',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'address' => 'required|string',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset all other default addresses
        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $address->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Address updated successfully',
            'data' => $address
        ]);
    }

    /**
     * Delete an address
     */
    public function destroy($id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        $address->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Address deleted successfully'
        ]);
    }
}