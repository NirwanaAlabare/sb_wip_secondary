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
        Route::get('/get-color', 'getColor')->name("get-color");
        Route::get('/get-size', 'getSize')->name("get-size");
        Route::get('/get-sewing-qty', 'getSewingQty')->name("get-sewing-qty");
        Route::get('/get-secondary-master', 'getSecondaryMaster')->name("get-get-secondary-master");
    });

    Route::controller(SecondaryInController::class)->prefix('secondary-in')->middleware("role:in")->group(function () {
        Route::get('/get-secondary-in-list', 'getSecondaryInList')->name("in-get-secondary-in-list");
        Route::get('/get-secondary-in-list-total', 'getSecondaryInListTotal')->name("in-get-secondary-in-list-total");

        Route::get('/get-secondary-in-out-daily', 'getSecondaryInOutDaily')->name("in-get-secondary-in-out-daily");
        Route::get('/get-secondary-in-out-detail', 'getSecondaryInOutDetail')->name("in-get-secondary-in-out-detail");
        Route::get('/get-secondary-in-out-detail-total', 'getSecondaryInOutDetailTotal')->name("in-get-secondary-in-out-detail-total");

        Route::post('/submit-secondary-in', 'submitSecondaryIn')->name("in-submit-secondary-in");

        Route::post('/export-secondary-in-out', 'exportSecondaryInOut')->name("in-export-secondary-in-out");
    });

    Route::controller(SecondaryOutController::class)->prefix('secondary-out')->middleware("role:out")->group(function () {
        Route::get('/get-secondary-in-wip-total', 'getSecondaryInWipTotal')->name('get-secondary-in-wip-total');

        Route::get('/get-secondary-out-list', 'getSecondaryOutList')->name("out-get-secondary-out-list");
        Route::get('/get-secondary-out-log', 'getSecondaryOutLog')->name("out-get-secondary-out-log");
        Route::get('/get-secondary-out-total', 'getSecondaryOutTotal')->name("out-get-secondary-out-total");

        Route::get('/get-secondary-out-log-single', 'getSecondaryOutLogSingle')->name("out-get-secondary-out-log-single");
        Route::get('/get-secondary-out-log-total', 'getSecondaryOutLogTotal')->name("out-get-secondary-out-log-total");

        Route::get('/get-secondary-in-out-daily', 'getSecondaryInOutDaily')->name("out-get-secondary-in-out-daily");
        Route::get('/get-secondary-in-out-detail', 'getSecondaryInOutDetail')->name("out-get-secondary-in-out-detail");
        Route::get('/get-secondary-in-out-detail-total', 'getSecondaryInOutDetailTotal')->name("out-get-secondary-in-out-detail-total");

        Route::post('/submit-secondary-out/rft', 'submitSecondaryOutRft')->name("out-submit-secondary-out-rft");
        Route::post('/submit-secondary-out/defect', 'submitSecondaryOutDefect')->name("out-submit-secondary-out-defect");
        Route::post('/submit-secondary-out/rework', 'submitSecondaryOutRework')->name("out-submit-secondary-out-rework");
        Route::post('/submit-secondary-out/cancel-rework', 'cancelSecondaryOutRework')->name("out-cancel-secondary-out-rework");
        Route::post('/submit-secondary-out/reject', 'submitSecondaryOutReject')->name("out-submit-secondary-out-reject");
        Route::post('/submit-secondary-out/reject-defect', 'submitSecondaryOutRejectDefect')->name("out-submit-secondary-out-reject-defect");
        Route::post('/submit-secondary-out/cancel-reject-defect', 'cancelSecondaryOutRejectDefect')->name("out-camcel-secondary-out-reject-defect");

        Route::post('/export-secondary-in-out', 'exportSecondaryInOut')->name("out-export-secondary-in-out");
    });

    Route::controller(ProfileController::class)->prefix('profile')->group(function () {
        // Route::get('/{id}', 'index');
        Route::put('/update/{id}', 'update')->middleware('auth');
    });
});
