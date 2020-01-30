<?php

namespace App\Http\Controllers;

use App\Sale;
use App\Client;
use App\Provider;
use Carbon\Carbon;
use App\SoldProduct;
use App\Transaction;
use App\PaymentMethod;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactionname = [
            'income' => 'Ingreso',
            'payment' => 'Pago',
            'expense' => 'Gasto',
            'transfer' => 'Transferencia'
        ];
        return view('transactions.index', [
            'transactions' => Transaction::orderBy('created_at', 'desc')->paginate(25),
            'transactionname' => $transactionname
        ]);
    }

    public function stats()
    {
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);
        $salesperiods = [];
        $transactionsperiods = [];

        $salesperiods['Dia'] = Sale::whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->get();
        $transactionsperiods['Dia'] = Transaction::whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->get();

        $salesperiods['Ayer'] = Sale::whereBetween('created_at', [Carbon::now()->subDay(1)->startOfDay(), Carbon::now()->subDay(1)->endOfDay()])->get();
        $transactionsperiods['Ayer'] = Transaction::whereBetween('created_at', [Carbon::now()->subDay(1)->startOfDay(), Carbon::now()->subDay(1)->endOfDay()])->get();

        $salesperiods['Semana'] = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
        $transactionsperiods['Semana'] = Transaction::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();

        $salesperiods['Mes'] = Sale::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->get();
        $transactionsperiods['Mes'] = Transaction::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->get();

        $salesperiods['Trimestre'] = Sale::whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()])->get();
        $transactionsperiods['Trimestre'] = Transaction::whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()])->get();

        $salesperiods['Año'] = Sale::whereYear('created_at', Carbon::now()->year)->get();
        $transactionsperiods['Año'] = Transaction::whereYear('created_at', Carbon::now()->year)->get();

        return view('transactions.stats', [
            'clients' => Client::where('balance', '!=', '0.00')->get(),
            'salesperiods' => $salesperiods,
            'transactionsperiods' => $transactionsperiods,
            'date' => Carbon::now(),
            'methods' => PaymentMethod::all()
        ]);
    }

    public function type($type)
    {
        switch($type) {
            case 'expense':
                return view('transactions.expense.index', ['transactions' => Transaction::where('type', 'expense')->orderBy('created_at', 'desc')->paginate(25)]);
                break;
            case 'payment':
                return view('transactions.payment.index', ['transactions' => Transaction::where('type', 'payment')->orderBy('created_at', 'desc')->paginate(25)]);
                break;
            case 'income':
                return view('transactions.income.index', ['transactions' => Transaction::where('type', 'income')->orderBy('created_at', 'desc')->paginate(25)]);
                break;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type)
    {
        switch($type) {
            case 'expense':
                return view('transactions.expense.create', [
                    'payment_methods' => PaymentMethod::all(),
                ]);
                break;
            case 'payment':
                return view('transactions.payment.create', [
                    'payment_methods' => PaymentMethod::all(),
                    'providers' => Provider::all(),
                ]);
                break;
            case 'income':
                return view('transactions.income.create', [
                    'payment_methods' => PaymentMethod::all(),
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Transaction $transaction)
    {
        if(isset($request->all()['client_id'])) {
            switch($request->all()['type']) {
                case 'income':
                    $request->merge(['title' => 'Pago Recibido de Cliente ID: '.$request->all()['client_id']]);
                    break;
                case 'expense':
                    $request->merge(['title' => 'Pago de Vuelto de Cliente ID: '.$request->all()['client_id']]);
                    if($request->all()['amount'] > 0) {
                        $request->merge(['amount' => ( (float) $request->all()['amount'] * (-1))]);
                    }
                    break;
            }
            $transaction->create($request->all());
            $client = Client::find($request->all()['client_id']);
            $client->balance += $request->all()['amount'];
            $client->save();
            return redirect()->route('clients.show', $request->all()['client_id'])->withStatus('Transacción registrada satisfactoriamente.');
        }
        switch($request->all()['type']) {
            case 'expense':
                if($request->all()['amount'] > 0) {
                    $request->merge(['amount' => ( (float) $request->all()['amount'] * (-1))]);
                }
                $transaction->create($request->all());
                return redirect()->route('transactions.type', ['type' => 'expense'])->withStatus('Gasto registrado satisfactoriamente.');
            case 'payment':
                if($request->all()['amount'] > 0) {
                    $request->merge(['amount' => ( (float) $request->all()['amount'] * (-1))]);
                }
                $transaction->create($request->all());
                return redirect()->route('transactions.type', ['type' => 'payment'])->withStatus('Pago registrado satisfactoriamente.');
            case 'income':
                $transaction->create($request->all());
                return redirect()->route('transactions.type', ['type' => 'income'])->withStatus('Ingreso registrado satisfactoriamente.');
            default:
                return redirect()->route('transactions.index')->withStatus('Transacción registrada satisfactoriamente.');
        }
    }

    /** 
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        switch($transaction->type) {
            case 'expense':
                return view('transactions.expense.edit', [
                    'transaction' => $transaction,
                    'payment_methods' => PaymentMethod::all()
                ]);
                break;
            case 'payment':
                return view('transactions.payment.edit', [
                    'transaction' => $transaction,
                    'payment_methods' => PaymentMethod::all(),
                    'providers' => Provider::all()
                ]);
                break;
            case 'income':
                return view('transactions.income.edit', [
                    'transaction' => $transaction,
                    'payment_methods' => PaymentMethod::all(),
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        $transaction->update($request->all());

        switch($request->all()['type']) {
            case 'expense':
                if($request->all()['amount'] > 0) {
                    $request->merge(['amount' => ( (float) $request->all()['amount'] * (-1))]);
                }
                return redirect()->route('transactions.type', ['type' => 'expense'])->withStatus('Gasto actualizado satisfactoriamente.');
                break;
            case 'payment':
                if($request->all()['amount'] > 0) {
                    $request->merge(['amount' => ( (float) $request->all()['amount'] * (-1))]);
                }
                return redirect()->route('transactions.type', ['type' => 'payment'])->withStatus('Pago actualizado satisfactoriamente.');
                break;
            case 'income':
                return redirect()->route('transactions.type', ['type' => 'income'])->withStatus('Ingreso actualizado satisfactoriamente.');
                break;
            default:
                return redirect()->route('transactions.index')->withStatus('Transacción actualizada satisfactoriamente.');
                break;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //if ($transaction->sale)
        //{
        //    return back()->withStatus('No puedes eliminar una transacción de una venta finalizada. Puedes eliminar la venta y su registro completo.');
        //}
        if ($transaction->transfer)
        {
            return back()->withStatus('No puedes eliminar una transacción de una transferencia. Debes eliminar la transferencia para eliminar sus registros.');
        }
        $type = $transaction->type;
        $transaction->delete();
        switch($type) {
            case 'expense':
                return back()->withStatus('Gasto eliminado satisfactoriamente.');
                break;
            case 'payment':
                return back()->withStatus('Pago eliminado satisfactoriamente.');
                break;
            case 'income':
                return back()->withStatus('Ingreso eliminado satisfactoriamente.');
                break;
            default:
                return back()->withStatus('Transacción eliminada satisfactoriamente.');
                break;
        }
    }
}
