<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

////// Category
Route::get('/categories', [\App\Http\Controllers\CategoryController::class,'index']);
Route::get('/categories/{category}', [\App\Http\Controllers\CategoryController::class,'show']);

///// Product
Route::get('/products', [\App\Http\Controllers\ProductController::class,'index']);
Route::get('/products/{product}', [\App\Http\Controllers\ProductController::class,'show']);
Route::get('/count', [\App\Http\Controllers\ProductController::class,'countCP']);

//// Size
Route::get('/sizes', [\App\Http\Controllers\SizeController::class,'index']);

//// Visit
Route::post('/visits', [\App\Http\Controllers\VisitController::class, 'store']);

Route::group(['middleware' => 'jwt.auth'], function () {
    ////// Category
    Route::post('/categories', [\App\Http\Controllers\CategoryController::class,'store']);
    Route::patch('/categories/{category}', [\App\Http\Controllers\CategoryController::class,'update']);
    Route::delete('/categories/{category}', [\App\Http\Controllers\CategoryController::class,'delete']);

    ///// Product
    Route::post('/products', [\App\Http\Controllers\ProductController::class,'store']);
    Route::patch('/products/{product}', [\App\Http\Controllers\ProductController::class,'update']);
    Route::delete('/products/{product}', [\App\Http\Controllers\ProductController::class,'delete']);

    ///// Size
    Route::post('/sizes', [\App\Http\Controllers\SizeController::class,'store']);
    Route::patch('/sizes/{size}', [\App\Http\Controllers\SizeController::class,'update']);
    Route::delete('/sizes/{size}', [\App\Http\Controllers\SizeController::class,'delete']);

    //// Visit
    Route::get('/visits', [\App\Http\Controllers\VisitController::class, 'get']);
});
