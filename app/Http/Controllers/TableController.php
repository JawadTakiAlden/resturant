<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTableRequest;
use App\Http\Resources\TableResource;
use App\Models\Table;
use App\Traits\CustomResponse;
use Illuminate\Http\Request;

class TableController extends Controller
{
    use CustomResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tables = Table::all();

        return TableResource::collection($tables);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTableRequest $request)
    {
        $request->validated($request->all());

        $table = Table::create($request->all());

        return TableResource::collection([$table]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Table $table)
    {
        return TableResource::collection([$table]);
    }
}
