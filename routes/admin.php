<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

/********* Admin Routes **********/
Route::get('/login', function () {
    return view('admin.auth.login');
})->name('admin.login');

Route::post('/login',[AdminController::class,'login'])->name('admin.login.submit');
    // Route::get('/dashboard', function () {
    //     return view('admin.pages.dashboard');
    // })->name('admin.dashboard');

Route::middleware('admin.session')->group(function () {
// dashboard
Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');


    // products management
Route::get('/products',function(){
    return view('admin.pages.products');
});
Route::get('/products/data',[AdminController::class,'productsData'])->name('admin.products.data');
Route::get('/products/create',[AdminController::class,'createProduct'])->name('admin.products.create');
// store product
Route::post('/products/store',[AdminController::class,'storeProduct'])->name('admin.products.store');
// edit product
Route::get('/products/edit/{id}',[AdminController::class,'editProduct'])->name('admin.products.edit');
// update product
Route::post('/products/update/{id}',[AdminController::class,'updateProduct'])->name('admin.products.update');


// // categories management


Route::get('/categories', function(){
    return view('admin.pages.categories');
})->name('admin.categories');
// get all categories data for datatable
Route::get('/categories/data', [AdminController::class, 'categoriesData'])->name('admin.categories.data');
Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('admin.categories.create');
Route::post('/categories/store', [AdminController::class, 'storeCategory'])->name('admin.categories.store');




// users


Route::get('/users', function () {
    return view('admin.pages.users');
})->name('users');
Route::get('/users/data', [AdminController::class, 'usersData'])->name('admin.users.data');


// orders

Route::get('/orders',function(){
    return view('admin.pages.orders');
});
// get data in data table
Route::get('/orders/data',[AdminController::class,'getOrders'])->name('getOrders');
Route::get('/orders/edit/{id}',[AdminController::class,'editOrder'])->name('admin.orders.edit');
Route::post('/orders/update-quantity/{id}', [AdminController::class, 'updateOrderQuantity'])->name('order.updateQuantity');
Route::post('/orders/update-order/{id}', [AdminController::class, 'updateOrder'])->name('order.updateOrder');


// logout
Route::get('/logout', function(){
   session()->forget('is_admin_logged_in'); // sirf admin flag hatao
    // ya full clear:
    // session()->flush();

    return redirect()->route('admin.login')
        ->with('success', 'Logged out successfully');

})->name('admin.logout');
Route::get('/write-debug', function () {

    $dir = public_path('uploads/products');
    $file = $dir . '/test.txt';

    return [
        'exists' => file_exists($dir),
        'is_dir' => is_dir($dir),
        'writable' => is_writable($dir),
        'realpath' => realpath($dir),
    ];
});
});
