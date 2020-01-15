<?php

namespace App\Http\Controllers;

use App\Sale;
use App\Product;
use Carbon\Carbon;
use App\SoldProduct;
use App\Transaction;
use App\PaymentMethod;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('sales.index', [
            'sales' => Sale::where('finalized_at', '!=', null)->orderBy('created_at', 'desc')->paginate(25),
            'unfinishedSales' => Sale::where('finalized_at', null)->orderBy('created_at', 'desc')->get()
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sales.create', ['clients' => \App\Client::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Sale $model)
    {
        $existent = Sale::where('client_id', $request->all()['client_id'])->where('finalized_at', null)->get();
        if($existent->count()) {
            return back()->withError('Ya existe una venta sin finalizad perteneciente a este cliente. <a href="'.route('sales.show', $existent->first()).'">Click aquí para ir a ella</a>');
        }
        $sale = $model->create($request->all());
        return redirect()->route('sales.show', ['sale' => $sale->id])->withStatus('Venta registrada satisfactoriamente, puede empezar a registrar productos y trasacciones.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {
        return view('sales.show', ['sale' => $sale]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('sales.index')->withStatus('El registro de venta ha sido eliminado satisfactoriamente.');
    }

    public function finalize(Sale $sale)
    {
        $sale->total_amount = $sale->products->sum('total_amount');
        $sale->finalized_at = Carbon::now()->toDateTimeString();
        $sale->save();
        return back()->withStatus('La venta ha sido finalizada satisfactoriamente.');
    }

    public function addproduct(Sale $sale)
    {
        return view('sales.addproduct', ['sale' => $sale, 'products' => \App\Product::all()]);
    }

    public function storeproduct(Request $request, Sale $sale, SoldProduct $soldProduct)
    {
        $request->merge(['total_amount' => ($request->all()['price'] * $request->all()['qty'])]);
        $soldProduct->create($request->all());
        return redirect()->route('sales.show', ['sale' => $sale])->withStatus('Producto registrado satisfactoriamente.');
    }

    public function editproduct(Sale $sale, SoldProduct $soldproduct)
    {
        return view('sales.editproduct', [
            'sale' => $sale,
            'soldproduct' => $soldproduct,
            'products' => Product::all()
        ]);
    }

    public function updateproduct(Request $request, Sale $sale, SoldProduct $soldproduct)
    {
        $request->merge(['total_amount' => ($request->all()['price'] * $request->all()['qty'])]);
        $soldproduct->update($request->all());
        return redirect()->route('sales.show', $sale)->withStatus('Producto modificado satisfactoriamente.');
    }

    public function destroyproduct(Sale $sale, SoldProduct $soldproduct)
    {
        $soldproduct->delete();
        return back()->withStatus('El producto ha sido eliminado satisfactoriamente');
    }

    public function addtransaction(Sale $sale)
    {
        return view('sales.addtransaction', ['sale' => $sale, 'payment_methods' => PaymentMethod::all()]);
    }

    public function storetransaction(Request $request, Sale $sale, Transaction $transaction)
    {
        switch($request->all()['type']) {
            case 'income':
                $request->merge(['title' => 'Pago Recibido de Venta ID: '.$request->all()['sale_id']]);
                break;
            case 'expense':
                $request->merge(['title' => 'Pago de Vuelto de Venta ID: '.$request->all()['sale_id']]);
                if($request->all()['amount'] > 0) {
                    $request->merge(['amount' => ( (float) $request->all()['amount'] * (-1))]);
                }
                break;
        }
        $transaction->create($request->all());
        return redirect()->route('sales.show', ['sale' => $sale])->withStatus('Transacción registrada satisfactoriamente.');
    }

    public function edittransaction(Sale $sale, Transaction $transaction)
    {
        return view('sales.edittransaction', [
            'sale' => $sale,
            'transaction' => $transaction,
            'payment_methods' => PaymentMethod::all()
        ]);
    }

    public function updatetransaction(Request $request, Sale $sale, Transaction $transaction)
    {
        switch($request->all()['type']) {
            case 'income':
                $request->merge(['title' => 'Pago Recibido de Venta ID: '.$request->all()['sale_id']]);
                break;
            case 'expense':
                $request->merge(['title' => 'Pago de Vuelto de Venta ID: '.$request->all()['sale_id']]);
                if($request->all()['amount'] > 0) {
                    $request->merge(['amount' => ( (float) $request->all()['amount'] * (-1))]);
                }
                break;
        }
        $transaction->update($request->all());
        return redirect()->route('sales.show', ['sale' => $sale])->withStatus('Transacción modificada satisfactoriamente.');
    }

    public function destroytransaction(Sale $sale, Transaction $transaction)
    {
        $transaction->delete();
        return back()->withStatus('Transacción eliminada satisfactoriamente.');
    }
}
