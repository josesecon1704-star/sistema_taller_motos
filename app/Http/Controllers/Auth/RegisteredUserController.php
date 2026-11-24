<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'           => ['required', 'string', 'max:100'],
            'tipo_documento'   => ['required', 'in:ci,nit,pasaporte'],
            'numero_documento' => ['required', 'string', 'max:20'],
            'telefono'         => ['required', 'string', 'max:20'],
            'email'            => ['required', 'string', 'lowercase', 'email', 'max:100', 'unique:users,email'],
            'password'         => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $yaExiste = Cliente::where('tipo_documento', $request->tipo_documento)
            ->where('numero_documento', $request->numero_documento)
            ->exists();

        if ($yaExiste) {
            throw ValidationException::withMessages([
                'numero_documento' => 'Este documento ya está registrado en el taller. Comunícate con el taller para activar tu cuenta.',
            ]);
        }

        $user = DB::transaction(function () use ($request) {
            $cliente = Cliente::create([
                'nombre'           => $request->nombre,
                'tipo_documento'   => $request->tipo_documento,
                'numero_documento' => $request->numero_documento,
                'telefono'         => $request->telefono,
                'tipo'             => 'particular',
            ]);

            return User::create([
                'email'      => $request->email,
                'password'   => $request->password,
                'estado'     => 'activo',
                'rol_id'     => Rol::where('nombre', 'Cliente')->value('id'),
                'cliente_id' => $cliente->id,
            ]);
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
