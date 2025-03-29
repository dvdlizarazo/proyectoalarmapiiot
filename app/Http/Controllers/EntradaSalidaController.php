<?php

namespace App\Http\Controllers;

use App\Models\EntradaSalida;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EntradaSalidaController extends Controller
{
    // Registrar la entrada o salida
    public function registrar(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'tipo' => 'required|string|in:entrada,salida', // Validar que el tipo sea "entrada" o "salida"
            'fecha_registro' => 'required|date_format:Y-m-d H:i:s',
        ]);

        // Crear el registro de entrada o salida
        $entradaSalida = EntradaSalida::create([
            'tipo' => $request->tipo,
            'id_usuario' => $request->id_usuario,
            'id_ubicacion' => 1,
            'fecha_registro' => Carbon::createFromFormat('Y-m-d H:i:s', $request->fecha_registro)->setTimezone('America/Bogota'), // Asegurarse de que la hora esté en la zona horaria de Colombia
        ]);

        // Respuesta en caso de éxito
        return response()->json([
            'message' => 'Entrada o salida registrada correctamente',
            'data' => $entradaSalida
        ], 201);
    }
}
