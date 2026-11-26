<?php

use App\Http\Controllers\BillReportController;
use App\Http\Controllers\ElectricityUsageController;
use App\Http\Controllers\TariffTierController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/usages');

Route::resource('usages', ElectricityUsageController::class);
Route::get('reports/bills', BillReportController::class)->name('reports.bills');
Route::get('tariffs', [TariffTierController::class, 'index'])->name('tariffs.index');
Route::get('tariffs/{buildingType:code}/edit', [TariffTierController::class, 'edit'])->name('tariffs.edit');
Route::put('tariffs/{buildingType:code}', [TariffTierController::class, 'update'])->name('tariffs.update');
