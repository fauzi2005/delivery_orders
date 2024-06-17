<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MasterProduct;
use App\Models\TransactionDeliveryOrder;
use App\Models\TransactionDocumentReference;
use App\Models\TransactionProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransationDeliveryOrderController extends Controller
{
    public function index()
    {
        try {
            $transactionDO = TransactionDeliveryOrder::all();

            $maps = [];

            foreach ($transactionDO as $transaction) {
                $map = new \stdClass();
                $map->id = $transaction->id;
                $map->transactionNumber = $transaction->transaction_number;
                $map->transactionDate = $transaction->date;
                $map->transactionCreatedDate = $transaction->created_at->toDateTimeString();
                $map->total = $transaction->total;
                $map->deliveryDestination = $transaction->deliveryDestination->name;
                $map->createdBy = $transaction->created_by;
                $map->productCount = $transaction->transactionProduct->count();
                $map->documentCount = $transaction->documentReference->count();

                $maps[] = $map;
            }

            return response()->json(['transactions' => $maps]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $transactionDO = TransactionDeliveryOrder::findOrFail($id);

            $map = new \stdClass();
            $map->transactionNumber = $transactionDO->transaction_number;
            $map->transactionDate = $transactionDO->date;
            $map->transactionCreatedDate = $transactionDO->created_at->toDateTimeString();
            $map->deliveryDestinationName = $transactionDO->deliveryDestination->name;
            $map->deliveryDestinationAddress = $transactionDO->deliveryDestination->address;
            $map->total = $transactionDO->total;
            $map->createdBy = $transactionDO->created_by;
            $map->products = [];
            $map->documentReferences = [];

            foreach ($transactionDO->transactionProduct as $products) {
                $product = new \stdClass();
                $product->id = $products->product->id;
                $product->code = $products->product->code;
                $product->name = $products->product->name;
                $product->price = $products->price;
                $product->stockOrder = $products->stock_order;
                $product->total = $products->total;

                $map->products[] = $product;
            }

            foreach ($transactionDO->documentReference as $documents) {
                $document = new \stdClass();
                $document->documentUrl = url(Storage::url($documents->document_url));

                $map->documentReferences[] = $document;
            }

            return response()->json($map);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Detail transaction not found'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'deliveryDestinationId' => 'required|exists:m_delivery_destinations,id',
                'products' => 'required|array',
                'products.*.id' => 'required|exists:m_products,id',
                'products.*.order' => 'required|integer',
                'documents' => 'nullable|array',
                'documents.*' => 'required|mimes:jpg,jpeg,png,pdf|max:5120'
            ]);

            $transaksi = DB::transaction(function () use ($validated) {
                $user = Auth::user();

                $transaksi = TransactionDeliveryOrder::create([
                    'transaction_number' => 'TRX' . now()->format('YmdHis'),
                    'date' => $validated['date'],
                    'destination_id' => $validated['deliveryDestinationId'],
                    'total' => 0,
                    'created_by' => $user->id,
                ]);

                $totalTransaction = 0;

                foreach ($validated['products'] as $productData) {
                    $product = MasterProduct::findOrFail($productData['id']);

                    if ($product->stock < $productData['order']) {
                        throw new \Exception('Stok produk tidak mencukupi');
                    }

                    $product->stock -= $productData['order'];
                    $product->save();

                    $totalTransaction += $product->price * $productData['order'];

                    TransactionProduct::create([
                        'transaction_id' => $transaksi->id,
                        'product_id' => $product->id,
                        'price' => $product->price,
                        'stock_order' => $productData['order'],
                        'total' => $product->price * $productData['order']
                    ]);
                }

                $transaksi->total = $totalTransaction;
                $transaksi->save();

                $documentArray = 0;

                if (!empty($validated['documents'])) {
                    foreach ($validated['documents'] as $file) {
                        $extension = $file->getClientOriginalExtension();

                        $prefix = in_array($extension, ['jpg', 'jpeg', 'png']) ? 'IMAGE' : 'DOCUMENT';

                        $filename = $prefix . '_' . now()->timestamp . '[' . $documentArray . ']' . '.' . $extension;

                        $path = $file->storeAs('do_reference', $filename, 'public');

                        TransactionDocumentReference::create([
                            'delivery_order_id' => $transaksi->id,
                            'document_url' => $path
                        ]);

                        $documentArray++;
                    }
                }

                return $transaksi;
            });

            return response()->json($transaksi, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
