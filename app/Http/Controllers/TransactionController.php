<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $transactions = Transaction::join('users as c', 'c.id', '=', 'customer_id')
            ->join('users as t', 't.id', '=', 'tourguide_id')
            ->join('products as p', 'p.id', '=', 'product_id')
            ->where('p.id', $input['id'])
            ->where('transactions.is_orders_done', 0)
            ->selectRaw("p.id as product_id, transactions.id, t.name as tourguide_name, c.name as customer_name, p.name as nama_wisata, p.price as harga_wisata, transactions.tanggal_wisata, transactions.metode_pembayaran")
            ->paginate();
        $output = array(
            "count" => count($transactions),
            "message" => "Get transactions succssfully!",
            "code" => 200,
            "data" => $transactions

        );
        return response()->json($output);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'tourguide_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'tanggal_wisata' => 'required',
            'metode_pembayaran' => 'required',
            'is_orders_done' => 'boolean',
        ]);

        if (!isset($validatedData['is_orders_done'])) {
            $validatedData['is_orders_done'] = 0;
        }

        $transaction = Transaction::create($validatedData);

        return response()->json($transaction, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }
        return response()->json($transaction);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $validatedData = $request->validate([
            'is_orders_done' => 'boolean',
        ]);

        $transaction->update($validatedData);

        return response()->json($transaction);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully']);
    }
}
