<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;
use App\Http\Resources\MealResource;
use App\Models\Meal;
use App\Traits\CustomResponse;

class MealController extends Controller
{
    use CustomResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
            if ($request->query()){
                return $this->customResponse(null , 'Query parameters not allowed' , 401);
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
        return MealResource::collection([$meal]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMealRequest $request, Meal $meal)
    {
        $request->validated($request->all());

        $meal->update($request->all());

        return $this->customResponse($meal , 'One Meal Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meal $meal)
    {
        $meal->delete();
        return $this->customResponse(null , 'One Meal Deleted Successfully');
    }

    public function switchMeal(Meal $meal){
        $meal->update([
           'visibility' => ! boolval($meal->visibility),
        ]);
    }
}
