@extends('layouts.app', ['page' => 'Información de Proveedor', 'pageSlug' => 'providers', 'section' => 'providers'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Información del Proveedor</h4>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Información de Pago</th>
                            <th>Pagos Realizados</th>
                            <th>Total Pagado</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $provider->id }}</td>
                                <td>{{ $provider->name }}</td>
                                <td>{{ $provider->description }}</td>
                                <td>{{ $provider->email }}</td>
                                <td>{{ $provider->phone }}</td>
                                <td style="max-width: 175px">{{ $provider->paymentinfo }}</td>
                                <td>{{ $provider->transactions->count() }}</td>
                                <td>{{ abs($provider->transactions->sum('amount')) }}$</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Pagos Realizados: {{ $transactions->count() }}</h4>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>Fecha</th>
                            <th>ID</th>
                            <th>Titulo</th>
                            <th>Método</th>
                            <th>Monto</th>
                            <th>Referencia</th>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td>{{ date('d-m-y', strtotime($transaction->created_at)) }}</td>
                                    <td>{{ $transaction->id }}</td>
                                    <td>{{ $transaction->title }}</td>
                                    <td><a href="{{ route('methods.show', $transaction->method) }}">{{ $transaction->method->name }}</a></td>
                                    <td>{{ $transaction->amount }}$</td>
                                    <td>{{ $transaction->reference }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-4">
                    <nav class="d-flex justify-content-end">
                        {{ $transactions->links() }}
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection
