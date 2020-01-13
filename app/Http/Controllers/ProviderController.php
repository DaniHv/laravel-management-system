<?php

namespace App\Http\Controllers;

use App\Provider;
use App\Transaction;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProviderRequest;

class ProviderController extends Controller
{
    /**
     * Display a listing of the Provs
     *
     * @param  \App\Provider  $model
     * @return \Illuminate\View\View
     */
    public function index(Provider $model)
    {
        return view('providers.index', ['providers' => $model->paginate(25)]);
    }

    /**
     * Show the form for creating a new Prov
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('providers.create');
    }

    /**
     * Store a newly created Provider in storage
     *
     * @param  \App\Http\Requests\ProviderRequest  $request
     * @param  \App\Provider  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProviderRequest $request, Provider $provider)
    {
        $provider->create($request->all());
        return redirect()->route('providers.index')->withStatus(__('Provider successfully created.'));
    }

    /**
     * Show the form for editing the specified Provider
     *
     * @param  \App\Provider  $Provider
     * @return \Illuminate\View\View
     */
    public function edit(Provider $Provider)
    {
        return view('providers.edit', compact('Provider'));
    }

    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Provider $provider)
    {
        return view('providers.show', [
            'provider' => $provider,
            'transactions' => Transaction::where('provider_id', $provider->id)->orderBy('created_at', 'desc')->paginate(25)
        ]);
    }

    /**
     * Update the specified Provider in storage
     *
     * @param  \App\Http\Requests\ProviderRequest  $request
     * @param  \App\Provider  $Provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProviderRequest $request, Provider $provider)
    {
        $provider->update($request->all());

        return redirect()->route('providers.index')->withStatus(__('Provider successfully updated.'));
    }

    /**
     * Remove the specified Provider from storage
     *
     * @param  \App\Provider  $Prov
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Provider $provider)
    {
        $provider->delete();

        return redirect()->route('providers.index')->withStatus(__('Provider successfully deleted.'));
    }
}
