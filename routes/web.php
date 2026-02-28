<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MyShopController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TodayItemController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;


// Guest routes (only accessible when not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes (require login)
Route::middleware('auth')->group(function () {
    // Dashboard routes - USE THIS VERSION (uncommented)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::prefix('myshop')->name('myshop.')->group(function () {
        Route::get('/', [MyShopController::class, 'index'])->name('index'); // user.myshop.index
        Route::get('/create', [MyShopController::class, 'create'])->name('create');
        Route::post('/', [MyShopController::class, 'store'])->name('store');
        Route::get('/{id}', [MyShopController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [MyShopController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MyShopController::class, 'update'])->name('update');
        Route::delete('/{myshop}', [MyShopController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('stock')->name('stock.')->group(function () {
    Route::get('/', [StockController::class, 'index'])->name('index');
    Route::post('/', [StockController::class, 'store'])->name('store');
    Route::get('/create', [StockController::class, 'create'])->name('create');
    Route::get('/{stock}', [StockController::class, 'show'])->name('show');
    Route::get('/{stock}/edit', [StockController::class, 'edit'])->name('edit');
    Route::put('/{stock}', [StockController::class, 'update'])->name('update');
    Route::delete('/{stock}', [StockController::class, 'destroy'])->name('destroy');
    Route::post('/check-item-code', [StockController::class, 'checkItemCode'])
        ->name('check-item-code');
    
    // Vendor routes
    Route::get('/vendor/{vendor}', [StockController::class, 'vendorShow'])->name('vendor.show');
});

 Route::prefix('today-item')->name('today_item.')->group(function () {
        Route::get('/', [TodayItemController::class, 'index'])->name('index');
        
        // Temporary session routes
        Route::post('/add-to-temp', [TodayItemController::class, 'addToTemp'])->name('add-to-temp');
        Route::delete('/remove-from-temp/{id}', [TodayItemController::class, 'removeFromTemp'])->name('remove-from-temp');
        Route::post('/clear-temp', [TodayItemController::class, 'clearTemp'])->name('clear-temp');
        
        // Save all temporary items
        Route::post('/save-all', [TodayItemController::class, 'saveAll'])->name('save-all');
        
        // Get stock details
        Route::get('/get-stock/{id}', [TodayItemController::class, 'getStockDetails'])->name('get-stock');
        
        // Delete saved items
        Route::delete('/{id}', [TodayItemController::class, 'destroy'])->name('destroy');
        Route::post('/clear-all-saved', [TodayItemController::class, 'clearAllSaved'])->name('clear-all-saved');

        // Inside your today-item group
        Route::post('/search-by-date', [TodayItemController::class, 'searchByDate'])->name('search-by-date');
        Route::post('/get-items-by-date', [TodayItemController::class, 'getItemsByDate'])->name('get-items-by-date');
    });


});

// Customer routes
Route::prefix('user')->name('user.')->middleware(['auth'])->group(function () {
    // Customer Management
    Route::resource('customer', CustomerController::class);
    
    // Fabric Calculation routes
    Route::get('customer/{customer}/fabric/create', [CustomerController::class, 'fabricCreate'])->name('customer.fabric.create');
    Route::post('customer/{customer}/fabric', [CustomerController::class, 'fabricStore'])->name('customer.fabric.store');
    Route::get('customer/{customer}/fabric/{fabric}/edit', [CustomerController::class, 'fabricEdit'])->name('customer.fabric.edit');
    Route::put('customer/{customer}/fabric/{fabric}', [CustomerController::class, 'fabricUpdate'])->name('customer.fabric.update');
    Route::delete('customer/{customer}/fabric/{fabric}', [CustomerController::class, 'fabricDestroy'])->name('customer.fabric.destroy');
    Route::post('user/customer/{customer}/fabric/store-multiple', [CustomerController::class, 'storeMultiple'])->name('customer.fabric.storeMultiple');
});

Route::middleware(['auth'])->group(function () {
    // Invoice routes
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{id}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::post('/invoices/{id}/add-advance', [InvoiceController::class, 'addAdvance'])->name('invoices.add-advance');
    Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    
    // AJAX route for getting customer details
    Route::get('/invoices/customer/{id}/details', [InvoiceController::class, 'getCustomerDetails'])->name('invoices.customer.details');
});

// Home redirect
Route::get('/', function () {
    return redirect('/login');
});

 Route::middleware('auth')->group(function () {
    
    Route::get('/cost-calculator', function () {
        return view('user.cost_calculater.index');
    })->name('cost.calculator');

});
