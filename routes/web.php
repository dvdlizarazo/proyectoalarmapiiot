<?php

use Illuminate\Support\Facades\Route;
use App\Models\MotionSensorStatus;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

//User Routes
Route::middleware(['auth','user-role:user'])->group(function()
{
    Route::get("/home",[App\Http\Controllers\HomeController::class, 'userHome'])->name("home");
});

//Admin Routes
Route::middleware(['auth','user-role:admin'])->group(function()
{
    Route::get("/admin/home",[App\Http\Controllers\HomeController::class, 'adminHome'])->name("admin.home");



    Route::get('/sensor-status', function () {
        $latestSensor = MotionSensorStatus::latest('id')->first();
        
        if ($latestSensor) {
            return response()->json([
                'status' => $latestSensor->status,
                'detected_at' => \Carbon\Carbon::parse($latestSensor->detected_at)->format('m/d/Y h:i A'),
            ]);
        }

        return response()->json([
            'status' => null,
            'detected_at' => null,
        ]);
    });


});


