<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\SecurityChecker\Checker;
use App\Traits\CustomResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
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
        $categories = Category::where('visibility' , true)->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }

        $request->validated($request->all());

        $category = Category::create($request->all());

        return $this->customResponse($category , 'One Category Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }
        return CategoryResource::collection([$category]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }
        $request->validated($request->all());

        $category->update($request->all());

        return $this->customResponse($category , 'One Category Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }
        $category->delete();

        return $this->customResponse($category , 'One Category Deleted Successfully');
    }

    public function switchCategory(Category $category){
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }
        $category->update([
            'visibility' => ! boolval($category->visibility),
        ]);
    }
}
