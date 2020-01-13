<?php

namespace App\Http\Controllers;

use App\Sale;
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

        $daySales = Sale::whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->get();
        $dayProducts = SoldProduct::whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->get();
        $dayTransactions = Transaction::whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->get();
        $daySalesTransactions = Transaction::whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->where('sale_id', '!=', null)->get();
        $salesperiods['Dia'] = [
            'sales' => $daySales->count(),
            'clients' => Sale::whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->distinct('client_id')->count(),
            'products' => $dayProducts->sum('qty'),
            'uniqueproducts' => SoldProduct::whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->distinct('product_id')->count(),
            'unfinalized' => $daySales->where('finalized_at', null)->count(),
            'transactions' => $daySalesTransactions->count(),
            'balance' => $daySalesTransactions->sum('amount'),
            'avg' => Transaction::selectRaw('sale_id, max(created_at), sum(amount) as total_amount')->whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->where('sale_id', '!=', null)->groupBy('sale_id')->get()->avg('total_amount')
        ];
        $transactionsperiods['Dia'] = [
            'transactions' => $dayTransactions->count(),
            'incomes' => $dayTransactions->where('amount', '>', 0)->sum('amount'),
            'expenses' => $dayTransactions->where('amount', '<', 0)->sum('amount'),
            'balance' => $dayTransactions->sum('amount')
        ];

        $weekSales = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
        $weekProducts = SoldProduct::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
        $weekTransactions = Transaction::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
        $weekSalesTransactions = Transaction::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('sale_id', '!=', null)->get();
        $salesperiods['Semana'] = [
            'sales' => $weekSales->count(),
            'clients' => Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->distinct('client_id')->count(),
            'products' => $weekProducts->sum('qty'),
            'uniqueproducts' => SoldProduct::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->distinct('product_id')->count(),
            'unfinalized' => $weekSales->where('finalized_at', null)->count(),
            'transactions' => $weekSalesTransactions->count(),
            'balance' => $weekSalesTransactions->sum('amount'),
            'avg' => Transaction::selectRaw('sale_id, max(created_at), sum(amount) as total_amount')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('sale_id', '!=', null)->groupBy('sale_id')->get()->avg('total_amount')
        ];
        $transactionsperiods['Semana'] = [
            'transactions' => $weekTransactions->count(),
            'incomes' => $weekTransactions->where('amount', '>', 0)->sum('amount'),
            'expenses' => $weekTransactions->where('amount', '<', 0)->sum('amount'),
            'balance' => $weekTransactions->sum('amount')
        ];

        $monthSales = Sale::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->get();
        $monthProducts = SoldProduct::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->get();
        $monthTransactions = Transaction::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->get();
        $monthSalesTransactions = Transaction::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where('sale_id', '!=', null)->get();
        $salesperiods['Mes'] = [
            'sales' => $monthSales->count(),
            'clients' => Sale::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->distinct('client_id')->count(),
            'products' => $monthProducts->sum('qty'),
            'uniqueproducts' => SoldProduct::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->distinct('product_id')->count(),
            'unfinalized' => $monthSales->where('finalized_at', null)->count(),
            'transactions' => $monthSalesTransactions->count(),
            'balance' => $monthSalesTransactions->sum('amount'),
            'avg' => Transaction::selectRaw('sale_id, max(created_at), sum(amount) as total_amount')->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where('sale_id', '!=', null)->groupBy('sale_id')->get()->avg('total_amount')
        ];
        $transactionsperiods['Mes'] = [
            'transactions' => $monthTransactions->count(),
            'incomes' => $monthTransactions->where('amount', '>', 0)->sum('amount'),
            'expenses' => $monthTransactions->where('amount', '<', 0)->sum('amount'),
            'balance' => $monthTransactions->sum('amount')
        ];

        $quarterSales = Sale::whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()])->get();
        $quarterProducts = SoldProduct::whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()])->get();
        $quarterTransactions = Transaction::whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()])->get();
        $quarterSalesTransactions = Transaction::whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()])->where('sale_id', '!=', null)->get();
        $salesperiods['Trimestre'] = [
            'sales' => $quarterSales->count(),
            'clients' => Sale::whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()])->distinct('client_id')->count(),
            'products' => $quarterProducts->sum('qty'),
            'uniqueproducts' => SoldProduct::whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()])->distinct('product_id')->count(),
            'unfinalized' => $quarterSales->where('finalized_at', null)->count(),
            'transactions' => $quarterSalesTransactions->count(),
            'balance' => $quarterSalesTransactions->sum('amount'),
            'avg' => Transaction::selectRaw('sale_id, max(created_at), sum(amount) as total_amount')->whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()])->where('sale_id', '!=', null)->groupBy('sale_id')->get()->avg('total_amount')
        ];
        $transactionsperiods['Trimestre'] = [
            'transactions' => $quarterTransactions->count(),
            'incomes' => $quarterTransactions->where('amount', '>', 0)->sum('amount'),
            'expenses' => $quarterTransactions->where('amount', '<', 0)->sum('amount'),
            'balance' => $quarterTransactions->sum('amount')
        ];

        $yearSales = Sale::whereYear('created_at', Carbon::now()->year)->get();
        $yearProducts = SoldProduct::whereYear('created_at', Carbon::now()->year)->get();
        $yearTransactions = Transaction::whereYear('created_at', Carbon::now()->year)->get();
        $yearSalesTransactions = Transaction::whereYear('created_at', Carbon::now()->year)->where('sale_id', '!=', null)->get();
        $salesperiods['Año'] = [
            'sales' => $yearSales->count(),
            'clients' => Sale::whereYear('created_at', Carbon::now()->year)->distinct('client_id')->count(),
            'products' => $yearProducts->sum('qty'),
            'uniqueproducts' => SoldProduct::whereYear('created_at', Carbon::now()->year)->distinct('product_id')->count(),
            'unfinalized' => $yearSales->where('finalized_at', null)->count(),
            'transactions' => $yearSalesTransactions->count(),
            'balance' => $yearSalesTransactions->sum('amount'),
            'avg' => Transaction::selectRaw('sale_id, max(created_at), sum(amount) as total_amount')->whereYear('created_at', Carbon::now()->year)->where('sale_id', '!=', null)->groupBy('sale_id')->get()->avg('total_amount')
        ];
        $transactionsperiods['Año'] = [
            'transactions' => $yearTransactions->count(),
            'incomes' => $yearTransactions->where('amount', '>', 0)->sum('amount'),
            'expenses' => $yearTransactions->where('amount', '<', 0)->sum('amount'),
            'balance' => $yearTransactions->sum('amount')
        ];
        

        return view('transactions.stats', [
            'salesperiods' => $salesperiods,
            'transactionsperiods' => $transactionsperiods,
            'date' => Carbon::now(),
            'methods' => PaymentMethod::all(),
            'yeartransactions' => $yearTransactions,
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
