<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MotionSensorStatus;

class MotionSensorController extends Controller
{
    /**
     * Obtener todos los registros de sensores.
     */
    public function index()
    {
        $sensors = MotionSensorStatus::all();
        return response()->json($sensors, 200);
    }

    /**
     * Registrar un nuevo estado de sensor.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'sensor_name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'detected_at' => 'nullable|date',
        ]);

        $sensor = MotionSensorStatus::create($validatedData);

        return response()->json([
            'message' => 'Sensor registrado con éxito',
            'data' => $sensor
        ], 201);
    }

    /**
     * Obtener un sensor específico.
     */
    public function show($id)
    {
        $sensor = MotionSensorStatus::find($id);

        if (!$sensor) {
            return response()->json(['message' => 'Sensor no encontrado'], 404);
        }

        return response()->json($sensor, 200);
    }

    /**
     * Actualizar un estado de sensor existente.
     */
    public function update(Request $request, $id)
    {
        $sensor = MotionSensorStatus::find($id);

        if (!$sensor) {
            return response()->json(['message' => 'Sensor no encontrado'], 404);
        }

        $validatedData = $request->validate([
            'sensor_name' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'detected_at' => 'nullable|date',
        ]);

        $sensor->update($validatedData);

        return response()->json([
            'message' => 'Sensor actualizado con éxito',
            'data' => $sensor
        ], 200);
    }

    /**
     * Eliminar un registro de sensor.
     */
    public function destroy($id)
    {
        $sensor = MotionSensorStatus::find($id);

        if (!$sensor) {
            return response()->json(['message' => 'Sensor no encontrado'], 404);
        }

        $sensor->delete();

        return response()->json(['message' => 'Sensor eliminado con éxito'], 200);
    }
}
