<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\CategoryController; //panggil controller category
use App\Http\Controllers\ProductController; //panggil controller product
use App\Http\Controllers\AuthController; //panggil controller user
use App\Http\Controllers\OAuthController;


//routing untuk register dan login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//routing untuk oauth2 google

Route::group(['middleware' => ['web']], function () {
    Route::get('/oauth/register', [OAuthController::class, 'redirect']);
    Route::get('/auth/google/callback', [OAuthController::class, 'callback']);
});



//routing untuk CRUD pada tabel category dengan middleware (otorisasi hanya admin)
Route::middleware(['category-auth'])->group(function() {
    Route::post('/categories', [CategoryController::class, 'Create']);
    Route::get('/categories', [CategoryController::class, 'Show']);
    Route::put('/categories/{id}', [CategoryController::class, 'Update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'Delete']);
});

//routing untuk CRUD pada tabel products dengan middleware (otorisasi untuk admin dan user)
Route::middleware(['product-auth'])->group(function() {
    Route::post('/products', [ProductController::class, 'Create']);
    Route::get('/products', [ProductController::class, 'Show']);
    Route::put('/products/{id}', [ProductController::class, 'Update']);
    Route::delete('/products/{id}', [ProductController::class, 'Delete']);
});