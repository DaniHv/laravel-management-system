@extends('layouts.app', ['page' => 'Información de Producto', 'pageSlug' => 'products', 'section' => 'inventory'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Información del Producto</h4>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>ID</th>
                            <th>Categoría</th>
                            <th>Nombre</th>
                            <th>Stock</th>
                            <th>Stock Defectuoso</th>
                            <th>Precio Base</th>
                            <th>Precio Promedio</th>
                            <th>Ventas Totales</th>
                            <th>Ingresos Producidos</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td><a href="{{ route('categories.show', $product->category) }}">{{ $product->category->name }}</a></td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>{{ $product->stock_defective }}</td>
                                <td>{{ $product->price }}</td>
                                <td>{{ round($product->solds->avg('price'), 2) }}</td>
                                <td>{{ $product->solds->sum('qty') }}</td>
                                <td>{{ $product->solds->sum('total_amount') }}</td>
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
                <div class="card-body">
                    <h4 class="card-title">Últimas Ventas</h4>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>Fecha</th>
                            <th>ID de Venta</th>
                            <th>Cantidad</th>
                            <th>Precio Unidad</th>
                            <th>Monto Total</th>
                            <th></th>
                        </thead>
                        <tbody>
                            @foreach ($solds as $sold)
                                <tr>
                                    <td>{{ date('d-m-y', strtotime($sold->created_at)) }}</td>
                                    <td><a href="{{ route('sales.show', $sold->sale) }}">{{ $sold->sale_id }}</a></td>
                                    <td>{{ $sold->qty }}</td>
                                    <td>{{ $sold->price }}</td>
                                    <td>{{ $sold->total_amount }}</td>
                                    <td class="td-actions text-right">
                                        <a href="{{ route('sales.show', $sold->sale) }}" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Ver Venta">
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
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Últimos Recibos</h4>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>Fecha</th>
                            <th>ID de Recibo</th>
                            <th>Titulo</th>
                            <th>Stock</th>
                            <th>Stock Defectuoso</th>
                            <th>Stock Total</th>
                            <th></th>
                        </thead>
                        <tbody>
                            @foreach ($receiveds as $received)
                                <tr>
                                    <td>{{ date('d-m-y', strtotime($received->created_at)) }}</td>
                                    <td><a href="{{ route('receipts.show', $received->receipt) }}">{{ $received->receipt_id }}</a></td>
                                    <td style="max-width:150px;">{{ $received->receipt->title }}</td>
                                    <td>{{ $received->stock }}</td>
                                    <td>{{ $received->stock_defective }}</td>
                                    <td>{{ $received->stock + $received->stock_defective }}</td>
                                    <td class="td-actions text-right">
                                        <a href="{{ route('receipts.show', $received->receipt) }}" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Ver Recibo">
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
@endsection
