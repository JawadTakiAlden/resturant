<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
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
        $categories = Category::where('visibility' , true)->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $request->validated($request->all());

        $category = Category::create($request->all());

        return $this->customResponse($category , 'One Category Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return CategoryResource::collection([$category]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $request->validated($request->all());

        $category->update($request->all());

        return $this->customResponse($category , 'One Category Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return $this->customResponse($category , 'One Category Deleted Successfully');
    }

    public function switchCategory(Category $category){
        $category->update([
            'visibility' => ! boolval($category->visibility),
        ]);
    }
}
