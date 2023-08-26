<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;
use App\Http\Resources\MealResource;
use App\Models\Meal;
use App\SecurityChecker\Checker;
use App\Traits\CustomResponse;

class MealController extends Controller
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

        $meals = Meal::whereHas('category', function($q) {
            $q->where('visibility', true);
        })->where('visibility', true)
            ->orderBy('id')
            ->get();

        return MealResource::collection($meals);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMealRequest $request)
    {
        try {
            if (Checker::isParamsFoundInRequest()){
                return Checker::CheckerResponse();
            }


            $request->validated($request->all());

            $meal = Meal::create($request->all());

            return $this->customResponse($meal , 'Your Meals Added Successfully');
        }catch (\Throwable $th){
            return $this->customResponse(null , $th->getMessage() , 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Meal $meal)
    {
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }
        return MealResource::collection([$meal]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMealRequest $request, Meal $meal)
    {
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }
        $request->validated($request->all());

        $meal->update($request->all());

        return $this->customResponse($meal , 'One Meal Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meal $meal)
    {
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }
        $meal->delete();
        return $this->customResponse(null , 'One Meal Deleted Successfully');
    }

    public function switchMeal(Meal $meal){
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }
        $meal->update([
           'visibility' => ! boolval($meal->visibility),
        ]);
    }
}
