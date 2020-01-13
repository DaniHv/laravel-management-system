@extends('layouts.app', ['page' => 'Administrar Venta', 'pageSlug' => 'sales', 'section' => 'transactions'])


@section('content')
    @include('alerts.success')
    @include('alerts.error')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">Resumen de Venta</h4>
                        </div>
                        @if (!$sale->finalized_at)
                            <div class="col-4 text-right">
                                @if (($sale->transactions->count() == 0) || ($sale->products->count() == 0))
                                    <form action="{{ route('sales.destroy', $sale) }}" method="post" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            Eliminar Venta
                                        </button>
                                    </form>
                                @elseif ($sale->transactions->sum('amount') == $sale->products->sum('total_amount'))
                                    <button type="button" class="btn btn-sm btn-primary" onclick="confirm('Confirmas querer finalizar esta venta? Sus registros no podrán ser modificados de ahora en más.') ? window.location.replace('{{ route('sales.finalize', $sale) }}') : ''">
                                        Finalizar Venta
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm btn-primary" onclick="confirm('ATENCIÓN: Las transacciones de esta venda no parecen coincidir con el costo de los productos, seguro quieres finalizarla? Sus registros no podrán ser modificados de ahora en más.') ? window.location.replace('{{ route('sales.finalize', $sale) }}') : ''">
                                        Finalizar Venta
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Cliente</th>
                            <th>Productos</th>
                            <th>Transacciones</th>
                            <th>Costo Total</th>
                            <th>Última Actualización</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $sale->id }}</td>
                                <td>{{ date('d-m-y', strtotime($sale->created_at)) }}</td>
                                <td>{{ $sale->user->name }}</td>
                                <td><a href="{{ route('clients.show', $sale->client) }}">{{ $sale->client->name }}<br>{{ $sale->client->document_type }}-{{ $sale->client->document_id }}</a></td>
                                <td>{{ $sale->products->sum('qty') }}</td>
                                <td>
                                    @if ($sale->transactions->sum('amount') < $sale->products->sum('total_amount'))
                                        <span style="color:red;">
                                            {{ $sale->transactions->count() }} - Total ({{ $sale->transactions->sum('amount') }}$)
                                            <br>
                                            {{ $sale->products->sum('total_amount') - $sale->transactions->sum('amount') }}$ Restantes
                                        </span>
                                    @elseif ($sale->transactions->sum('amount') > $sale->products->sum('total_amount'))
                                        <span style="color:orange;">
                                            {{ $sale->transactions->count() }} - Total ({{ $sale->transactions->sum('amount') }}$)
                                            <br>
                                            {{ $sale->transactions->sum('amount') - $sale->products->sum('total_amount') }}$ Vuelto Restante
                                        </span>
                                    @else
                                        <span style="color:green;">{{ $sale->transactions->count() }} - Total ({{ $sale->transactions->sum('amount') }}$)</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($sale->transactions->sum('amount') < $sale->products->sum('total_amount'))
                                        <span style="color:red;">{{ $sale->products->sum('total_amount') }}$</span>
                                    @elseif ($sale->transactions->sum('amount') > $sale->products->sum('total_amount'))
                                        <span style="color:orange;">{{ $sale->products->sum('total_amount') }}$</span>
                                    @else
                                        <span style="color:green;">{{ $sale->products->sum('total_amount') }}$</span>
                                    @endif
                                    <br>
                                </td>
                                <td>{{ date('d-m-y', strtotime($sale->updated_at)) }}</td>
                                <td>{!! $sale->finalized_at ? 'Finalizado al<br>'.date('d-m-y', strtotime($sale->finalized_at)) : (($sale->transactions->sum('amount') == $sale->products->sum('total_amount')) ? 'POR FINALIZAR' : 'EN ESPERA') !!}</td>
                            </tr>
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
                            <h4 class="card-title">Productos: {{ $sale->products->sum('qty') }}</h4>
                        </div>
                        @if (!$sale->finalized_at)
                            <div class="col-4 text-right">
                                <a href="{{ route('sales.product.add', ['sale' => $sale->id]) }}" class="btn btn-sm btn-primary">Añadir</a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-full-width table-responsive">
                        <table class="table table-sorter">
                            <thead>
                                <th>Categoría</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach ($sale->products as $sold_product)
                                    <tr>
                                        <td>{{ $sold_product->product->category->name }}</td>
                                        <td>{{ $sold_product->product->name }}</td>
                                        <td>{{ $sold_product->qty }}</td>
                                        <td>{{ $sold_product->total_amount }}$</td>
                                        <td class="td-actions text-right">
                                            @if(!$sale->finalized_at)
                                                <a href="{{ route('sales.product.edit', ['sale' => $sale, 'soldproduct' => $sold_product]) }}" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Editar Pedido">
                                                    <i class="tim-icons icon-pencil"></i>
                                                </a>
                                                <form action="{{ route('sales.product.destroy', ['sale' => $sale, 'soldproduct' => $sold_product]) }}" method="post" class="d-inline">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Eliminar Pedido" onclick="confirm('Estás seguro que quieres eliminar este pedido de producto/s? Su registro será eliminado de esta venta.') ? this.parentElement.submit() : ''">
                                                        <i class="tim-icons icon-simple-remove"></i>
                                                    </button>
                                                </form>
                                            @endif
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
                            <h4 class="card-title">Transacciones: {{ $sale->transactions->count() }}</h4>
                        </div>
                        @if (!$sale->finalized_at)
                            <div class="col-4 text-right">
                                <a href="{{ route('sales.transaction.add', ['sale' => $sale->id]) }}" class="btn btn-sm btn-primary">Añadir</a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-full-width table-responsive">
                        <table class="table table-sorter">
                            <thead>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Método</th>
                                <th>Monto</th>
                                <th>Referencia</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach ($sale->transactions as $transaction)
                                    <tr>
                                        <td>{{ date('d-m-y', strtotime($transaction->created_at)) }}</td>
                                        <td>
                                        @if ($transaction->type == 'income')
                                            Pago
                                        @else
                                            Vuelto
                                        @endif
                                        </td>
                                        <td>{{ $transaction->method->name }}</td>
                                        <td>{{ $transaction->amount }}$</td>
                                        <td>{{ $transaction->reference }}</td>
                                        <td class="td-actions text-right">
                                            @if(!$sale->finalized_at)
                                                <a href="{{ route('sales.transaction.edit', ['sale' => $sale, 'transaction' => $transaction]) }}" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Editar Transacción">
                                                    <i class="tim-icons icon-pencil"></i>
                                                </a>
                                                <form action="{{ route('sales.transaction.destroy', ['sale' => $sale, 'transaction' => $transaction]) }}" method="post" class="d-inline">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Eliminar Transacción" onclick="confirm('Estás seguro que quieres eliminar esta transacción? Su registro será eliminado de esta venta.') ? this.parentElement.submit() : ''">
                                                        <i class="tim-icons icon-simple-remove"></i>
                                                    </button>
                                                </form>
                                            @endif
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
@endsection

@push('js')
    <script src="{{ asset('assets') }}/js/sweetalerts2.js"></script>
@endpush