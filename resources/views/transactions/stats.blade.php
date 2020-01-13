@extends('layouts.app', ['pageSlug' => 'tstats', 'page' => 'Estadísticas', 'section' => 'transactions'])

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-tasks">
                <div class="card-header">
                    <h4 class="card-title">Estadísticas de Transacciones</h4>
                </div>
                <div class="card-body">
                    <div class="table-full-width table-responsive">
                        <table class="table">
                            <thead>
                                <th>Periodo</th>
                                <th>Transacciones</th>
                                <th>Ingresos</th>
                                <th>Egresos</th>
                                <th>Balance</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach ($transactionsperiods as $period => $data)
                                    <tr>
                                        <td>{{ $period }}</td>
                                        <td>{{ $data['transactions'] }}</td>
                                        <td>{{ $data['incomes'] }}$</td>
                                        <td>{{ $data['expenses'] }}$</td>
                                        <td>{{ $data['balance'] }}$</td>
                                        <td></td>
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
                    <h4 class="card-title">Estadísticas por Método</h4>
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
                                        <td>{{ $method->name }}</td>
                                        <td>{{ $yeartransactions->where('payment_method_id', $method->id)->count() }}</td>
                                        <td>{{ $yeartransactions->where('payment_method_id', $method->id)->sum('amount') }}$</td>
                                        <td>{{ $method->name }}</td>
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
                <h4 class="card-title">Estadísticas de Ventas</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <th>Periodo</th>
                        <th>Ventas</th>
                        <th>Clientes</th>
                        <th>Productos</th>
                        <th>Productos (Únicos)</th>
                        <th>Transacciones</th>
                        <th>Balance</th>
                        <th data-toggle="tooltip" data-placement="bottom" title="Promedio de ingresos por cada venta">Promedio C/V</th>
                        <th>Por Finalizar</th>
                    </thead>
                    <tbody>
                        @foreach ($salesperiods as $period => $info)
                            <tr>
                                <td>{{ $period }}</td>
                                <td>{{ $info['sales'] }}</td>
                                <td>{{ $info['clients'] }}</td>
                                <td>{{ $info['products'] }}</td>
                                <td>{{ $info['uniqueproducts'] }}</td>
                                <td>{{ $info['transactions'] }}</td>
                                <td>{{ $info['balance'] }}$</td>
                                <td>{{ round($info['avg'], 2) }}$</td>
                                <td>{{ $info['unfinalized'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>
@endsection