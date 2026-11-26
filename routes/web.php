<?php

use App\Http\Controllers\BillReportController;
use App\Http\Controllers\ElectricityUsageController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/usages');

Route::resource('usages', ElectricityUsageController::class);
Route::get('reports/bills', BillReportController::class)->name('reports.bills');

// Added in later steps:
// Route::get('reports/bills', BillReportController::class)->name('reports.bills');
// Route::get('tariffs', [TariffTierController::class, 'index'])->name('tariffs.index');
// Route::put('tariffs/{buildingType}', [TariffTierController::class, 'update'])->name('tariffs.update');

// Route::get('/', function () {
//     return view('welcome');
// });
