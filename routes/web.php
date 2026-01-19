<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DefectInOutController;
use App\Http\Controllers\SecondaryInController;
use App\Http\Controllers\SecondaryOutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::controller(LoginController::class)->prefix('login')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/', 'index')->name('login');
        Route::post('/authenticate', 'authenticate');
    });

    Route::post('/unauthenticate', 'unauthenticate')->middleware('auth');
});


Route::middleware('auth')->group(function () {
    Route::get('/', [GeneralController::class, 'index']);

    Route::get('/in', function () {
        return view('index-in', ["mode" => "in"]);
    });

    Route::get('/out', [ProductionController::class, 'index']);

    Route::controller(GeneralController::class)->prefix('general')->group(function () {
        Route::get('/get-master-plan', 'getMasterPlan')->name("get-master-plan");
        Route::get('/get-size', 'getSize')->name("get-size");
        Route::get('/get-secondary-master', 'getSecondaryMaster')->name("get-get-secondary-master");
    });

    Route::controller(SecondaryInController::class)->prefix('secondary-in')->middleware("role:in")->group(function () {
        Route::get('/get-secondary-in-list', 'getSecondaryInList')->name("in-get-secondary-in-list");

        Route::get('/get-secondary-in-out-daily', 'getSecondaryInOutDaily')->name("in-get-secondary-in-out-daily");
        Route::get('/get-secondary-in-out-detail', 'getSecondaryInOutDetail')->name("in-get-secondary-in-out-detail");
        Route::get('/get-secondary-in-out-detail-total', 'getSecondaryInOutDetailTotal')->name("in-get-secondary-in-out-detail-total");

        Route::post('/submit-secondary-in', 'submitSecondaryIn')->name("in-submit-secondary-in");

        Route::post('/export-secondary-in-out', 'exportSecondaryInOut')->name("in-export-secondary-in-out");
    });

    Route::controller(SecondaryOutController::class)->prefix('secondary-out')->middleware("role:out")->group(function () {
        Route::get('/get-secondary-out-list', 'getSecondaryOutList')->name("out-get-secondary-out-list");
        Route::get('/get-secondary-out-log', 'getSecondaryOutLog')->name("out-get-secondary-out-log");

        Route::get('/get-secondary-in-out-daily', 'getSecondaryInOutDaily')->name("out-get-secondary-in-out-daily");
        Route::get('/get-secondary-in-out-detail', 'getSecondaryInOutDetail')->name("out-get-secondary-in-out-detail");
        Route::get('/get-secondary-in-out-detail-total', 'getSecondaryInOutDetailTotal')->name("out-get-secondary-in-out-detail-total");

        Route::post('/export-secondary-in-out', 'exportSecondaryInOut')->name("out-export-secondary-in-out");
    });

    // Route::controller(DefectInOutController::class)->prefix('defect-in-out')->group(function () {
    //     Route::get('/get-master-plan', 'getMasterPlan')->name("get-master-plan");
    //     Route::get('/get-size', 'getSize')->name("get-size");
    //     Route::get('/get-defect-type', 'getDefectType')->name("get-defect-type");
    //     Route::get('/get-defect-area', 'getDefectArea')->name("get-defect-area");

    //     Route::get('/get-defect-in-list', 'getDefectInList')->name("get-defect-in-list");
    //     Route::get('/get-defect-out-list', 'getDefectOutList')->name("get-defect-out-list");

    //     Route::get('/get-defect-in-out-daily', 'getDefectInOutDaily')->name("get-defect-in-out-daily");
    //     Route::get('/get-defect-in-out-detail', 'getDefectInOutDetail')->name("get-defect-in-out-detail");
    //     Route::get('/get-defect-in-out-detail-total', 'getDefectInOutDetailTotal')->name("get-defect-in-out-detail-total");

    //     Route::post('/submit-defect-in', 'submitDefectIn')->name("submit-defect-in");
    //     Route::post('/submit-defect-out', 'submitDefectOut')->name("submit-defect-out");

    //     Route::post('/export-defect-in-out', 'exportDefectInOut')->name("export-defect-in-out");
    // });

    Route::controller(ProductionController::class)->prefix('production-panel')->group(function () {
        Route::get('/{id}', 'index');
        Route::post('/unauthenticate', 'unauthenticate')->middleware('auth');
        Route::post('/unauthenticate', 'unauthenticate')->middleware('auth');
    });

    Route::controller(ProfileController::class)->prefix('profile')->group(function () {
        // Route::get('/{id}', 'index');
        Route::put('/update/{id}', 'update')->middleware('auth');
    });
});
