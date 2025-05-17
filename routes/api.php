<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

//admin api
Route::group(['middleware' => ['auth:sanctum','checkRoleAdmin']],function (){
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::apiResource('/users', \App\Http\Controllers\admin\UserController::class ); //users api
    Route::resource('/categories', \App\Http\Controllers\admin\CategoriesController::class); //category api
    Route::resource('brands', \App\Http\Controllers\admin\BrandController::class);  //brand api
    Route::resource('/discount-codes', \App\Http\Controllers\admin\DiscountCodeController::class); //category api

    Route::resource('/products', \App\Http\Controllers\admin\ProductController::class);  //product api
    Route::post('/temp-images', [\App\Http\Controllers\admin\TempImageController::class, 'store']);
    Route::post('/save-product-image/{id}', [\App\Http\Controllers\admin\ProductController::class, 'saveProductImage']);
    Route::get('/update-default-image', [\App\Http\Controllers\admin\ProductController::class, 'updateDefaultImage']);
    Route::delete('/delete-product-image/{id}', [\App\Http\Controllers\admin\ProductController::class, 'deleteProductImage']);
    Route::delete('/delete-product/{id}', [\App\Http\Controllers\admin\ProductController::class, 'destroy']);

    Route::apiResource('/orders', \App\Http\Controllers\admin\OrderController::class);

    Route::get('/total-products', [\App\Http\Controllers\admin\DashBoardController::class, 'totalProducts']);
    Route::get('/total-paid-product', [\App\Http\Controllers\admin\DashBoardController::class, 'totalPaidOrders']);
    Route::get('/total-users', [\App\Http\Controllers\admin\DashBoardController::class, 'totalUser']);
    Route::get('/total-sale-product', [\App\Http\Controllers\admin\DashBoardController::class, 'totalSaleProduct']);
    Route::get('/top-sale-product', [\App\Http\Controllers\admin\DashBoardController::class, 'top5SaleProducts']);
});

//client api
Route::group(['middleware' => ['auth:sanctum']],function (){
    Route::get('/all-products', [\App\Http\Controllers\client\ProductController::class, 'index']);
    Route::get('/featured-products', [\App\Http\Controllers\client\ProductController::class, 'featuredProduct']);
    Route::get('/product-detail/{id}', [\App\Http\Controllers\client\ProductController::class, 'showDetail']);
    Route::get('/order-confirmation/{id}', [\App\Http\Controllers\client\OrderController::class, 'show']);
    Route::get('/category-product/{id}', [\App\Http\Controllers\client\ProductController::class, 'categoryProduct']);
    Route::get('/brand-product/{id}', [\App\Http\Controllers\client\ProductController::class, 'brandProduct']);
    Route::get('/product-search/', [\App\Http\Controllers\client\ProductController::class, 'searchByName']);

    Route::get('/order-history/{id}', [\App\Http\Controllers\client\OrderController::class, 'getOrderHistory' ]);
    Route::put('/cancel-order/{id}', [\App\Http\Controllers\client\OrderController::class, 'cancelOrder']);
    Route::put('/shipped-order/{id}', [\App\Http\Controllers\client\OrderController::class, 'shippedOrder']);
    Route::put('/refunded-order/{id}', [\App\Http\Controllers\client\OrderController::class, 'refundedOrder']);

    Route::get('/all-categories', [\App\Http\Controllers\client\ProductController::class, 'categories']);
    Route::get('/all-brands', [\App\Http\Controllers\client\ProductController::class, 'brands']);
    Route::get('/get-category/{id}', [\App\Http\Controllers\client\ProductController::class, 'getCategory']);
    Route::get('/get-brand/{id}', [\App\Http\Controllers\client\ProductController::class, 'getBrand']);

    Route::put('/update-user/{id}', [\App\Http\Controllers\client\UserController::class, 'update']);
    Route::get('/all-discount-code', [\App\Http\Controllers\client\ProductController::class, 'getDiscountCode']);
});
Route::group(['middleware' => ['auth:sanctum', 'checkUser']],function (){
    Route::post('/save-order', [\App\Http\Controllers\client\OrderController::class, 'saveOrder']);  // neu khong dang nhap se khong tao duoc don hang

});

//api dang ky, dang nhap
Route::post('/register',[\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);


