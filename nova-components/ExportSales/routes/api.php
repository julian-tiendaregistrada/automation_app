<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Vendor\ExportSales\Http\Controllers\ExportSalesController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

// Route::get('/', function (Request $request) {
//     //
// });

Route::post('/export', [ExportSalesController::class, 'export']);
Route::post('/analyze/{taskId}', [ExportSalesController::class, 'analyzeExport']);

Route::get('/tasks', [ExportSalesController::class, 'getTasks']);
Route::get('/categories', [ExportSalesController::class, 'getCategories']);
Route::get('/subcategories', [ExportSalesController::class, 'getSubcategories']);
