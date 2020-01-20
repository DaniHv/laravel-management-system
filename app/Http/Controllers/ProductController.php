<?php

namespace App\Http\Controllers;

use App\Product;
use App\SoldProduct;
use App\ReceivedProduct;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $model)
    {
        return view('inventory.products.index', [
            'products' => $model->paginate(25),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('inventory.products.create', [
            'categories' => \App\ProductCategory::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\ProductRequest  $request
     * @param  App\Product  $model
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request, Product $model)
    {
        $model->create($request->all());
        return redirect()->route('products.index')->withStatus('Producto registrado satisfactoriamente.');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return view('inventory.products.show', [
            'product' => $product,
            'solds' => SoldProduct::where('product_id', $product->id)->orderBy('created_at', 'desc')->limit(25)->get(),
            'receiveds' => ReceivedProduct::where('product_id', $product->id)->orderBy('created_at', 'desc')->limit(25)->get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('inventory.products.edit', [
            'product' => $product,
            'categories' => \App\ProductCategory::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->all());
        return redirect()->route('products.index')->withStatus('Producto actualizado satisfactoriamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->withStatus('Producto eliminado satisfactoriamente.');
    }
}
