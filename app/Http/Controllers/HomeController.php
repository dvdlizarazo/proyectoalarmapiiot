<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntradaSalida;
use App\Models\MotionSensorStatus;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function adminHome()
    {
        $entradasSalidas = EntradaSalida::with(['usuario', 'ubicacion'])->get();
    
        // Cambiar a paginación (10 registros por página)
        $sensors = MotionSensorStatus::latest('detected_at')->paginate(10);
        
        // Último estado del sensor
        $latestSensor = MotionSensorStatus::latest('detected_at')->first();

        return view('admin.home', [
            'entradasSalidas' => $entradasSalidas,
            'sensors' => $sensors,
            'latestSensor' => $latestSensor
        ]);
    }

    
    public function userHome()
    {
        return view('user.home',["msg"=>"Hello! I am user"]);
    }
}
