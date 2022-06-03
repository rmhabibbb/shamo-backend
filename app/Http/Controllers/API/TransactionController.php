<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $status = $request->input('status');

        if ($id) {
            $transactions =  Transaction::with(['items.product'])->find($id);

            if ($transactions) {
                return ResponseFormatter::success($transactions, 'Data transaction berhasil diambil');
            } else {
                return ResponseFormatter::error(null, 'Data tidak ada', 404);
            }
        }

        $transactions =  Transaction::with(['items.product'])->where('users_id', Auth::user()->id);

        if ($status) {
            $transactions->where('status', $status);
        }
        return ResponseFormatter::success(
            $transactions->paginate($limit),
            'Data berhasil diambil'
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'exists:products,id',
            'total_price' => 'required',
            'shipping_price' => 'required',
            'status' => 'required|in:PENDING,SUCCESS,CANCELLED,FAILED,SHIPPING,SHIPPED',
        ]);

        $transaction = Transaction::create([
            'users_id' => Auth::user()->id,
            'address' => $request->address,
            'total_price' => $request->total_price,
            'shipping_price' => $request->shipping_price,
            'status' => $request->status
        ]);

        foreach ($request->items as $product) {
            TransactionItem::create([
                'users_id' => Auth::user()->id,
                'products_id' => $product['id'],
                'transactions_id' => $transaction->id,
                'quantity' => $product['quantity']
            ]);
        }

        return ResponseFormatter::success($transaction->load('items.product'), 'Transaksi berhasil');
    }
}
