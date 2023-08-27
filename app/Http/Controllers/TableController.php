<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTableRequest;
use App\Http\Resources\TableResource;
use App\Models\Order;
use App\Models\Table;
use App\SecurityChecker\Checker;
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

        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }
        $tables = Table::all();

        return TableResource::collection($tables);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTableRequest $request)
    {
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }
        $request->validated($request->all());

        $table = Table::create($request->all());

        return TableResource::collection([$table]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Table $table)
    {
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }
        return TableResource::collection([$table]);
    }

    public function closeTable(Table $table){
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }
        $table->update([
           'in_progress' => false
        ]);


        $order = Order::where('table_id' , $table['id'])->where('in_progress' , true)->first();

        $order->update([
            'in_progress' => false
        ]);

        return $this->customResponse($table , 'Your request was successfully and table number' . $table['table_number'] . 'is free now');
    }
}
