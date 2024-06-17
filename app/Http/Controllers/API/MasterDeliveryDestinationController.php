<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MasterDeliveryDestination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MasterDeliveryDestinationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = MasterDeliveryDestination::query();

            if ($request->has('searchKey')) {
                $query->where('name', 'like', '%' . $request->searchKey . '%');
            }

            return response()->json(['deliveryDestinations' => $query->get()], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'name' => 'required',
                'address' => 'required',
            ]);

            $validated['created_by'] = $user->id;

            $destination = MasterDeliveryDestination::create($validated);

            return response()->json($destination, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $destination = MasterDeliveryDestination::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required',
                'address' => 'required',
            ]);

            $destination->update($validated);

            return response()->json($destination);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $destination = MasterDeliveryDestination::findOrFail($id);

            if ($destination->transactionDO()->exists()) {
                return response()->json(['error' => 'Tujuan pengiriman tidak dapat dihapus karena sudah masuk transaksi DO'], 400);
            }

            $destination->delete();

            return response()->json(['message' => 'Delivery destination successfully deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Delivery destination not found.'], 500);
        }
    }
}
