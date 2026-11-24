<?php

namespace App\Http\Controllers;

use App\Models\Garantia;
use Illuminate\Http\Request;

class GarantiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $garantias = Garantia::with(['ordenTrabajo', 'cliente', 'reclamos'])->latest()->get();

        return view('garantias.index', compact('garantias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Garantia $garantia)
    {
        $garantia->load(['ordenTrabajo', 'cliente', 'reclamos']);

        return response()->json($garantia);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Garantia $garantia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Garantia $garantia)
    {
        $request->validate([
            'dias' => 'nullable|integer|min:1',
            'km' => 'nullable|integer|min:1',
            'cobertura' => 'nullable|string|max:255',
            'estado' => 'nullable|string|in:activa,vencida,anulada',
        ]);

        $garantia->update($request->only(['dias', 'km', 'cobertura', 'estado']));

        return redirect()->back()->with('success', 'Garantía actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Garantia $garantia)
    {
        //
    }
}