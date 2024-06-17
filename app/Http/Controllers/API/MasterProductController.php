<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MasterProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MasterProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = MasterProduct::query();

            if ($request->has('searchKey')) {
                $query->where('name', 'like', '%' . $request->searchKey . '%')
                    ->orWhere('code', 'like', '%' . $request->searchKey . '%');
            }

            return response()->json(['products' => $query->get()]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'code' => 'required|unique:m_products',
                'name' => 'required',
                'description' => 'nullable',
                'price' => 'required|numeric',
                'stock' => 'required|integer'
            ]);

            $validated['created_by'] = $user->id;

            $product = MasterProduct::create($validated);

            return response()->json($product, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $product = MasterProduct::findOrFail($id);

            $validated = $request->validate([
                'code' => 'required|unique:m_products,code,' . $product->id,
                'name' => 'required',
                'description' => 'nullable',
                'price' => 'required|numeric',
                'stock' => 'required|integer'
            ]);

            $product->update($validated);

            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = MasterProduct::findOrFail($id);

            if ($product->transactionProduct()->exists()) {
                return response()->json(['error' => 'Produk tidak dapat dihapus karena sudah masuk transaksi DO'], 400);
            }

            $product->delete();

            return response()->json(['message' => "Product has been deleted"], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Product not found'], 500);
        }
    }
}
