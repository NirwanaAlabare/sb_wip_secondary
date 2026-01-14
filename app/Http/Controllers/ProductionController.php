<?php

namespace App\Http\Controllers;

use App\Models\SignalBit\MasterPlan;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;

class ProductionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('index-out', ['mode' => 'out']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LineProductions  $lineProductions
     * @return \Illuminate\Http\Response
     */
    public function show(LineProductions $lineProductions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LineProductions  $lineProductions
     * @return \Illuminate\Http\Response
     */
    public function edit(LineProductions $lineProductions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LineProductions  $lineProductions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LineProductions $lineProductions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LineProductions  $lineProductions
     * @return \Illuminate\Http\Response
     */
    public function destroy(LineProductions $lineProductions)
    {
        //
    }

    public function universal() {
        return Redirect::to('/');

        return view('production-panel-universal');
    }

    public function temporary() {
        return Redirect::to('/');

        return view('production-panel-temporary');
    }
}
