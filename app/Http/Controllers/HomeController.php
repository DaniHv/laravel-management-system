<?php

namespace App\Http\Controllers;
use App\Sale;
use Carbon\Carbon;
use App\SoldProduct;
use App\Transaction;
use App\PaymentMethod;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        $methodslist = PaymentMethod::all();
        $monthlyBalanceByMethod = [];
        $monthlyBalance = 0;
        foreach ($methodslist as $method) {
            $balance = Transaction::where('payment_method_id', $method->id)->whereMonth('created_at', Carbon::now()->month)->sum('amount');
            $monthlyBalance += (float) $balance;
            $monthlyBalanceByMethod[$method->name] = $balance;
        }

        $anualsales = '[';
        for ($i=1; $i <= 12; $i++) { 
            $monthsale = Sale::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', $i)->count();
            $anualsales .= $monthsale.',';
        }
        $anualsales .= ']';
        $anualclients = '[';
        for ($i=1; $i <= 12; $i++) { 
            $monthclients = Sale::selectRaw('count(distinct client_id) as total')->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', $i)->first();
            $anualclients .= $monthclients->total.',';
        }
        $anualclients .= ']';
        $anualproducts = '[';
        for ($i=1; $i <= 12; $i++) { 
            $monthproducts = SoldProduct::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', $i)->sum('qty');
            $anualproducts .= $monthproducts.',';
        }        
        $anualproducts .= ']';

        $actualmonth = Carbon::now()->locale('es');
        $lastmonths = [];
        $lastincomes = '';
        $lastexpenses = '';
        $semesterincomes = 0;
        $semesterexpenses = 0;
        for ($i=1; $i <= 6; $i++) {
            array_push($lastmonths, $actualmonth->shortMonthName);
            $incomes = Transaction::where('type', 'income')->whereYear('created_at', $actualmonth->year)->WhereMonth('created_at', $actualmonth->month)->sum('amount');
            $semesterincomes += $incomes;
            $lastincomes = round($incomes).','.$lastincomes;
            $expenses = abs(Transaction::whereIn('type', ['expense', 'payment'])->whereYear('created_at', $actualmonth->year)->WhereMonth('created_at', $actualmonth->month)->sum('amount'));
            $semesterexpenses += $expenses;
            $lastexpenses = round($expenses).','.$lastexpenses;
            $actualmonth->subMonth(1);
        }
        $lastincomes = '['.$lastincomes.']';
        $lastexpenses = '['.$lastexpenses.']';
        
        return view('dashboard', [
            'monthlybalance' => $monthlyBalance,
            'monthlybalancebymethod' => $monthlyBalanceByMethod,
            'lasttransactions' => Transaction::orderBy('created_at', 'desc')->limit(20)->get(),
            'unfinishedsales' => Sale::where('finalized_at', null)->get(),
            'anualsales' => $anualsales,
            'anualclients' => $anualclients,
            'anualproducts' => $anualproducts,
            'lastmonths' => array_reverse($lastmonths),
            'lastincomes' => $lastincomes,
            'lastexpenses' => $lastexpenses,
            'semesterexpenses' => $semesterexpenses,
            'semesterincomes' => $semesterincomes
        ]);
    }
}
