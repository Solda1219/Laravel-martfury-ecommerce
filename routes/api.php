<?php

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
use App\Http\Controllers\Controller;

Route::get('getAllproducts', [Controller::class, 'getAllproducts'])->name('products.getAll');
Route::get('getProductsByCategoryId', [Controller::class, 'getProductsByCategoryId'])->name('products.getProductsByCategoryId');
Route::get('getAllCategories', [Controller::class, 'getAllCategories'])->name('categories.getAll');