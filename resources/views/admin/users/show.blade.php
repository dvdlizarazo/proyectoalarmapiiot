@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Detalles del Usuario</span>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">Volver a la lista</a>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end fw-bold">ID:</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ $user->id }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end fw-bold">Nombre:</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ $user->name }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end fw-bold">Email:</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end fw-bold">Rol:</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ ucfirst($user->role) }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end fw-bold">Estado:</label>
                        <div class="col-md-6">
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end fw-bold">Fecha de registro:</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ $user->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end fw-bold">Última actualización:</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ $user->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">Editar</a>
                            <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-{{ $user->is_active ? 'secondary' : 'success' }}">
                                    {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection