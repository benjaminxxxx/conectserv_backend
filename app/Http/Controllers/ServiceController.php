<?php

namespace App\Http\Controllers;
use App\Models\Servicio;
use App\Http\Resources\ServiceResource;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ServiceResource::collection(Servicio::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $service = Servicio::create(['nombre' => $request->name]);
        return new ServiceResource($service);
    }

    /**
     * Display the specified resource.
     */
    public function show(Servicio $service)
    {
        return new ServiceResource($service);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Servicio $service)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $service->update(['nombre' => $request->name]);
        return new ServiceResource($service);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Servicio $service)
    {
        $service->delete();
        return response()->json(['message' => 'Servicio eliminado'], 200);
    }
}
