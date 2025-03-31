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
    public function adminHome(Request $request)
    {
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');

        $query = EntradaSalida::with(['usuario', 'ubicacion']);
        $sensorQuery = MotionSensorStatus::query();

        // Aplicar filtro de fechas si están presentes
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha_registro', [$fecha_inicio, $fecha_fin]);
            $sensorQuery->whereBetween('detected_at', [$fecha_inicio, $fecha_fin]);
        }

        $entradasSalidas = $query->get();
        $sensors = $sensorQuery->latest('detected_at')->paginate(10);
        $latestSensor = $sensorQuery->latest('detected_at')->first();

        // Contar entradas y salidas dentro del rango de fechas
        $entradas = (clone $query)->where('tipo', 'entrada')->count();
        $salidas = (clone $query)->where('tipo', 'salida')->count();

        // Contar movimientos del sensor dentro del rango de fechas
        $movimientos_activos = (clone $sensorQuery)->where('status', 1)->count();
        $movimientos_inactivos = (clone $sensorQuery)->where('status', 0)->count();

        return view('admin.home', [
            'entradasSalidas' => $entradasSalidas,
            'sensors' => $sensors,
            'latestSensor' => $latestSensor,
            'entradas' => $entradas,
            'salidas' => $salidas,
            'movimientos_activos' => $movimientos_activos,
            'movimientos_inactivos' => $movimientos_inactivos,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ]);
    }


    
    public function userHome()
    {
        return view('user.home',["msg"=>"Hello! I am user"]);
    }
}
