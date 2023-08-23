<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\TableController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login' , [AuthenticationController::class , 'login']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/orders' , [OrderController::class , 'index']);
    Route::get('/orders/{order}' , [OrderController::class , 'show']);
});

Route::middleware(['CheckWaiter' , 'auth:sanctum'])->group(function () {
    Route::post('/add_order_to_table' , [OrderItemController::class , 'store']);
});


Route::middleware(['CheckCasher' , 'auth:sanctum'])->group(function () {
    Route::patch('/accept_order/{order}' , [OrderController::class , 'acceptOrder']);
});

Route::middleware(['CheckKitchen' , 'auth:sanctum'])->group(function () {
    Route::patch('/start_preparing/{order}' , [OrderController::class , 'startPreparing']);
    Route::patch('/make_order_ready/{order}' , [OrderController::class , 'toReady']);
});


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/meals' , [MealController::class , 'index']);
    Route::get('/meals/{meal}' , [MealController::class , 'show']);
    Route::get('/categories' , [CategoryController::class , 'index']);
    Route::get('/categories/{category}' , [CategoryController::class , 'show']);
  Route::get('/tables' , [TableController::class , 'index']);
Route::get('/tables/{table}' , [TableController::class , 'show']);
});

Route::middleware(['CheckAdmin' , 'auth:sanctum'])->group(function () {
    Route::post('/meals' , [MealController::class , 'store']);
    Route::patch('/meals/{meal}' , [MealController::class , 'update']);
    Route::post('/categories' , [CategoryController::class , 'store']);
    Route::patch('/switch_category/{category}' , [CategoryController::class , 'switchCategory']);
    Route::patch('/categories/{category}' , [CategoryController::class , 'update']);
    Route::post('/tables' , [TableController::class , 'store']);
    Route::patch('/switch_meal/{meal}' , [MealController::class , 'switchMeal']);
});





