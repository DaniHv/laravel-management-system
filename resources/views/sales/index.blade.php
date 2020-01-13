@extends('layouts.app', ['page' => 'Ventas', 'pageSlug' => 'sales', 'section' => 'transactions'])

@section('content')
    @include('alerts.success')
    <div class="row">
        <div class="col-md-6">
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">Ventas Finalizadas</h4>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('sales.create') }}" class="btn btn-sm btn-primary">Registrar Venta</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="">
                        <table class="table">
                            <thead>
                                <th data-toggle="tooltip" data-placement="bottom" title="Fecha de Inicio / Fecha de Finalización">Fechas I/F</th>
                                <th>Cliente</th>
                                <th>Productos</th>
                                <th>Monto</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                    @if (!$sale->finalized_at)
                                        @continue
                                    @endif
                                    <tr>
                                        <td>{{ date('d-m-y', strtotime($sale->created_at)) }}<br>{{ date('d-m-y', strtotime($sale->created_at)) }}</td>
                                        <td><a href="{{ route('clients.show', $sale->client) }}">{{ $sale->client->name }}<br>{{ $sale->client->document_type }}-{{ $sale->client->document_id }}</a></td>
                                        <td>{{ $sale->products->sum('qty') }}</td>
                                        <td>{{ $sale->transactions->sum('amount') }}$</td>
                                        <td class="td-actions text-right">
                                            <a href="{{ route('sales.show', ['sale' => $sale]) }}" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Ver Venta">
                                                <i class="tim-icons icon-zoom-split"></i>
                                            </a>
                                            <form action="{{ route('sales.destroy', $sale) }}" method="post" class="d-inline">
                                                @csrf
                                                @method('delete')
                                                <button type="button" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Eliminar Venta" onclick="confirm('Estás seguro que quieres eliminar esta venta? Todos sus registros serán eliminados permanentemente.') ? this.parentElement.submit() : ''">
                                                    <i class="tim-icons icon-simple-remove"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-4">
                    <nav class="d-flex justify-content-end" aria-label="...">
                        {{ $sales->links() }}
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                        <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">Ventas Pendientes</h4>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('sales.create') }}" class="btn btn-sm btn-primary">Registrar Venta</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Productos</th>
                            <th data-toggle="tooltip" data-placement="bottom" title="Monto Pagado / Monto Total">Monto P/T</th>
                            <th></th>
                        </thead>
                        <tbody>
                            @foreach ($unfinishedSales as $sale)
                                @if ($sale->finalized_at)
                                    @continue
                                @endif
                                <tr>
                                    <td>{{ date('d-m-y', strtotime($sale->created_at)) }}</td>
                                    <td><a href="{{ route('clients.show', $sale->client) }}">{{ $sale->client->name }}<br>{{ $sale->client->document_type }}-{{ $sale->client->document_id }}</a></td>
                                    <td>{{ $sale->products->count() }}</td>
                                    <td >{{ $sale->transactions->sum('amount') }}$ / {{ $sale->products->sum('total_amount') }}$</td>
                                    <td class="td-actions text-right">
                                        <a href="{{ route('sales.show', ['sale' => $sale]) }}" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Editar Venta">
                                            <i class="tim-icons icon-pencil"></i>
                                        </a>
                                        <form action="{{ route('sales.destroy', $sale) }}" method="post" class="d-inline">
                                            @csrf
                                            @method('delete')
                                            <button type="button" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Eliminar Venta" onclick="confirm('Estás seguro que quieres eliminar esta venta? Todos sus registros serán eliminados permanentemente.') ? this.parentElement.submit() : ''">
                                                <i class="tim-icons icon-simple-remove"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
