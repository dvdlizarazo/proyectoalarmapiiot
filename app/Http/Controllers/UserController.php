<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Mostrar una lista de todos los usuarios.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(): \Illuminate\Contracts\View\View
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Mostrar el formulario para crear un nuevo usuario.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(): \Illuminate\Contracts\View\View
    {
        return view('admin.users.create');
    }

    /**
     * Almacenar un nuevo usuario en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'sometimes|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'user',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Usuario creado correctamente');
    }

    /**
     * Mostrar un usuario específico.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(User $user): \Illuminate\Contracts\View\View
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Mostrar el formulario para editar un usuario específico.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(User $user): \Illuminate\Contracts\View\View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Actualizar un usuario específico en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'sometimes|string|min:8|confirmed',
            'role' => 'sometimes|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'] ?? $user->role,
            'is_active' => isset($validated['is_active']) ? $validated['is_active'] : $user->is_active,
        ];

        // Solo actualizar la contraseña si se proporcionó una nueva
        if (isset($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Eliminar un usuario específico de la base de datos.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user): \Illuminate\Http\RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'Usuario eliminado correctamente');
    }

    /**
     * Cambiar el estado activo/inactivo de un usuario.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive(User $user): \Illuminate\Http\RedirectResponse
    {
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activado' : 'desactivado';
        return redirect()->route('admin.users.index')
                         ->with('success', "Usuario {$status} correctamente");
    }
}