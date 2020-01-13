<?php

namespace App\Http\Controllers;

use App\Transfer;
use App\Transaction;
use App\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Transfer $transfer)
    {
        return view('transfers.index', [
            'transfers' => Transfer::orderBy('created_at', 'desc')->paginate(25)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('transfers.create', [
            'methods' => PaymentMethod::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Transfer $transfer, Transaction $transaction)
    {
        $transfer = $transfer->create($request->all());
        $transaction->create([
            "type" => "expense",
            "title" => "Transferencia ID: ".$transfer->id,
            "transfer_id" => $transfer->id,
            "payment_method_id" => $transfer->sender_method_id,
            "amount" => ((float) abs($transfer->sended_amount) * (-1)),
            "user_id" => Auth::id(),
            "reference" => $transfer->reference
        ]);
        $transaction->create([
            "type" => "income",
            "title" => "Transferencia ID: ".$transfer->id,
            "transfer_id" => $transfer->id,
            "payment_method_id" => $transfer->receiver_method_id,
            "amount" => abs($transfer->received_amount),
            "user_id" => Auth::id(),
            "reference" => $transfer->reference
        ]);
        return redirect()->route('transfer.index')->withStatus('Transacción registrada satisfactoriamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transfer $transfer)
    {
        $transfer->delete();
        return back()->withStatus('La transferencia y sus transacciones asociadas han sido eliminadas satisfactoriamente.');
    }
}
