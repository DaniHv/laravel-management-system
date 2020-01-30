@extends('layouts.app', ['pageSlug' => 'tstats', 'page' => 'Estadísticas', 'section' => 'transactions'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">Estadísticas de Transacciones</h4>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-primary">Ver Transacciones</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                        <table class="table">
                            <thead>
                                <th>Periodo</th>
                                <th>Transacciones</th>
                                <th>Ingresos</th>
                                <th>Egresos</th>
                                <th>Balance en Bs</th>
                                <th>Balance Efectivo</th>
                                <th>Balance Total</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach ($transactionsperiods as $period => $data)
                                    <tr>
                                        <td>{{ $period }}</td>
                                        <td>{{ $data->count() }}</td>
                                        <td>{{ $data->where('type', 'income')->sum('amount') }}$</td>
                                        <td>{{ $data->where('type', 'expense')->sum('amount') }}$</td>
                                        <td>{{ $data->whereIn('payment_method_id', $methods->map(function ($method) {
                                            $VESaccounts = ['Banesco'];
                                            if(in_array($method->name, $VESaccounts)) return $method->id;
                                        }))->sum('amount') }}$</td>
                                        <td>{{ $data->where('payment_method_id', $methods->where('name', 'Efectivo')->first()->id)->sum('amount') }}$</td>
                                        <td>{{ $data->sum('amount') }}$</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-tasks">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">Balances Pendientes</h4>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('sales.create') }}" class="btn btn-sm btn-primary">Ver Clientes</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-full-width table-responsive">
                        <table class="table">
                            <thead>
                                <th>Cliente</th>
                                <th>Compras</th>
                                <th>Transacciones</th>
                                <th>Balance</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach($clients as $client)
                                    <tr>
                                        <td><a href="{{ route('clients.show', $client) }}">{{ $client->name }}<br>{{ $client->document_type }}-{{ $client->document_id }}</a></td>
                                        <td>{{ $client->sales->count() }}</td>
                                        <td>{{ $client->transactions->sum('amount') }}</td>
                                        <td>
                                            @if ($client->balance > 0)
                                                <span style="color:green">{{ $client->balance }}$</span>
                                            @elseif ($client->balance < 0.00)
                                                <span style="color:red">{{ $client->balance }}$</span>
                                            @else
                                                {{ $client->balance }}$
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('clients.transactions.add', ['client' => $client]) }}" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Registrar Transacción">
                                                <i class="tim-icons icon-simple-add"></i>
                                            </a>
                                            <a href="{{ route('clients.show', ['client' => $client]) }}" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Ver Client">
                                                <i class="tim-icons icon-zoom-split"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-tasks">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">Estadísticas por Métodos</h4>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('sales.create') }}" class="btn btn-sm btn-primary">Ver Métodos</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-full-width table-responsive">
                        <table class="table">
                            <thead>
                                <th>Método</th>
                                <th>Transacciones {{ $date->year }}</th>
                                <th>Balance {{ $date->year }}</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach($methods as $method)
                                    <tr>
                                        <td><a href="{{ route('methods.show', ['method' => $method]) }}">{{ $method->name }}</a></td>
                                        <td>{{ $transactionsperiods['Año']->where('payment_method_id', $method->id)->count() }}$</td>
                                        <td>{{ $transactionsperiods['Año']->where('payment_method_id', $method->id)->sum('amount') }}$</td>
                                        <td>
                                            <a href="{{ route('methods.show', ['method' => $method]) }}" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Ver Método">
                                                <i class="tim-icons icon-zoom-split"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-8">
                        <h4 class="card-title">Estadísticas de Ventas</h4>
                    </div>
                    <div class="col-4 text-right">
                        <a href="{{ route('sales.index') }}" class="btn btn-sm btn-primary">Ver Ventas</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <th>Periodo</th>
                        <th>Ventas</th>
                        <th>Clientes</th>
                        <th>Stock Total</th>
                        <th data-toggle="tooltip" data-placement="bottom" title="Promedio de ingresos por cada venta">Promedio C/V</th>
                        <th>Monto Facturado</th>
                        <th>Por Finalizar</th>
                    </thead>
                    <tbody>
                        @foreach ($salesperiods as $period => $data)
                            <tr>
                                <td>{{ $period }}</td>
                                <td>{{ $data->count() }}</td>
                                <td>{{ $data->groupBy('client_id')->count() }}</td>
                                <td>{{ $data->where('finalized_at', '!=', null)->map(function ($sale) {
                                    return $sale->products->sum('qty');
                                })->sum() }}</td>
                                <td>{{ $data->avg('total_amount') }}$</td>
                                <td>{{ $data->where('finalized_at', '!=', null)->map(function ($sale) {
                                    return $sale->products->sum('total_amount');
                                })->sum() }}$</td>
                                <td>{{ $data->where('finalized_at', null)->count() }}</td>
                            </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>
@endsection