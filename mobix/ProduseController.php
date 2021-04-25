<?php

namespace App\Http\Controllers;

use App\Produse;
use App\Corpuri;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class ProduseController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index()
    {
        $produse = Produse::orderBy('created_at', 'desc')->paginate(10);
       	return view('produse.index')->with('produse', $produse);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     */
    public function create()
    {
        // Create controller resource routes
        return view('produse.adauga');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function store(Request $request)
    {
        // Creaza un nou produs 
        
        $produs = new Produse;
        $produs->denumire = $request->input('denumire');
        $slug = SlugService::createSlug(Produse::class, 'slug', $produs->denumire);
        $produs->slug = $slug;
        $produs->save();

        return redirect('/produse')->with('success', 'Produs adăugat cu succes');
    }

    /**
     * Display the specified resource.
     *
     * @param Produse $id
     * @return void
     */
    public function show(Produse $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $slug
     * @param $id
     * @return Factory|View
     */
    public function edit($slug, $id)
    {
        // $produs = Produse::find($id);
        $produs = Produse::where('slug', $slug)->firstOrFail();
        $id = Produse::find($id);
        $corpuri = Corpuri::where('prod_id', $produs->id)->orderBy('denumire', 'asc')->paginate(10);
        return view('produse.editeaza', compact('produs', 'id', 'corpuri'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Redirector
     */
    public function update(Request $request, $id)
    {
        $produs = Produse::find($id);
        $produs->denumire = $request->input('denumire');
        $produs->save();

        return redirect('/produse')->with('success', 'Produs actualizat cu succes');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return RedirectResponse|Redirector
     */
    public function destroy($id)
    {
        $produs = Produse::find($id);
        $produs->delete();

        return redirect('/produse')->with('success', 'Produs șters cu succes');
    }
}
