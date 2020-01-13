@extends('layouts.app', ['page' => 'Información de Cliente', 'pageSlug' => 'clients', 'section' => 'clients'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Información del Cliente</h4>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Documento</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Compras</th>
                            <th>Última Compra</th>
                            <th>Total Pagado</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $client->id }}</td>
                                <td>{{ $client->name }}</td>
                                <td>{{ $client->document_type }}-{{ $client->document_id }}</td>
                                <td>{{ $client->phone }}</td>
                                <td>{{ $client->email }}</td>
                                <td>{{ $client->sales->count() }}</td>
                                <td>{{ date('d-m-y', strtotime($sales->first()->created_at)) }}</td>
                                <td>{{ $client->transactions->sum('amount') }}$</td>
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
                    <h4 class="card-title">Compras: {{ $sales->count() }}</h4>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Productos</th>
                            <th>Monto</th>
                            <th>Pagado</th>
                            <th>Transacciones</th>
                            <th>Estado</th>
                            <th></th>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr>
                                    <td><a href="{{ route('sales.show', $sale) }}">{{ $sale->id }}</a></td>
                                    <td>{{ date('d-m-y', strtotime($sale->created_at)) }}</td>
                                    <td>{{ $sale->products->sum('qty') }}</td>
                                    <td>{{ $sale->products->sum('total_amount') }}</td>
                                    <td>{{ $sale->transactions->sum('amount') }}</td>
                                    <td>{{ $sale->transactions->count() }}</td>
                                    <td>{{ ($sale->finalized_at) ? 'FINALIZADA' : 'EN ESPERA' }}</td>
                                    <td class="td-actions text-right">
                                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Más Detalles">
                                            <i class="tim-icons icon-zoom-split"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-4">
                    <nav class="d-flex justify-content-end">
                        {{ $sales->links() }}
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection