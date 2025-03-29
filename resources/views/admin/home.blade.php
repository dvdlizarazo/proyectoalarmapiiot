@extends('layouts.admin')

@section('content')
    <div class="bg-primary pt-10 pb-21"></div>
    <div class="container-fluid mt-n22 px-6">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <!-- Page header -->
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="mb-2 mb-lg-0">
                            <h3 class="mb-0  text-white">Bienvenido {{ ucfirst(Auth::user()->role) }}</h3>
                        </div>
                        
                    </div>
                </div>
            </div>
     
    
        </div>
   
        <!-- row  -->
        <div class="row my-6">
            <div class="col-xl-4 col-lg-12 col-md-12 col-12 mb-6 mb-xl-0">
                <!-- card  -->
                <div class="card h-100">
                    <!-- card body  -->
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0">Estado sensor de movimiento</h4>
                                
                                @if($latestSensor)
                                    <h1 id="sensor-status" class="{{ $latestSensor->status ? 'text-success' : 'text-danger' }}">
                                        {{ $latestSensor->status === 1 ? 'ACTIVO' : 'INACTIVO' }}
                                    </h1>
                                    <p id="sensor-detected-at" class="mb-0">
                                        Última detección: {{ \Carbon\Carbon::parse($latestSensor->detected_at)->format('m/d/Y h:i A') }}
                                    </p>
                                @else
                                    <h1 id="sensor-status" class="text-secondary">SIN REGISTROS</h1>
                                    <p id="sensor-detected-at" class="mb-0"></p>
                                @endif
                            </div>
                        </div>
                        
                        
                        <!-- chart  -->
                        <div class=" mb-8">
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle text-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>
                                                <i class="mdi mdi-sensor"></i> Nombre Sensor
                                            </th>
                                            <th>
                                                <i class="mdi mdi-calendar-clock"></i> Fecha Detección
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sensors as $sensor)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="ms-3">
                                                            <h6 class="mb-0">{{ $sensor->sensor_name }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($sensor->detected_at)->format('m/d/Y h:i A') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Paginación -->
                            <div class="d-flex justify-content-center mt-3">
                                <nav>
                                    {{ $sensors->links() }}
                                </nav>
                            </div>
                            
                            
                        </div>
                        <!-- icon with content  -->
                        
                    </div>
                </div>
            </div>
            <!-- card  -->
            <div class="col-xl-8 col-lg-12 col-md-12 col-12">
                <div class="card h-100">
                    <!-- card header  -->
                    <div class="card-header bg-white py-4">
                        <h4 class="mb-0">Control de acceso </h4>
                    </div>
                    <!-- table  -->
                    <div class="table-responsive">
                        <table class="table text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Ubicación</th>
                                    <th>Fecha</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entradasSalidas as $registro)
                                    <tr>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="ms-3 lh-1">
                                                    <h5 class="mb-1">{{ $registro->usuario->name }}</h5>
                                                    <p class="mb-0">{{ $registro->usuario->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <!-- Condición para mostrar el badge según el tipo -->
                                            @if($registro->tipo == 'entrada')
                                                <span class="badge bg-success view-details cursor-pointer">Entrada</span>
                                            @else
                                                <span class="badge bg-danger view-details cursor-pointer">Salida</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">{{ $registro->ubicacion->nombre_ubicacion }}</td>
                                        <td class="align-middle">{{ $registro->fecha_registro->format('d M, Y h:ia') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    
@endsection

