<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Observers\BitacoraObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(BitacoraObserver::class)]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'estado',
        'ultimo_acceso',
        'rol_id',
        'cliente_id',
        'empleado_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ultimo_acceso' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //devuelve el nombre del empleado o cliente, y si no hay, el email. Sin esto, las vistas de abajo mostrarían el nombre vacío.
    protected function name(): Attribute
    {
        return Attribute::get(
            fn() => $this->empleado?->nombre ?? $this->cliente?->nombre ?? $this->email
        );
    }

    protected ?array $permisosCache = null;

    public function tienePermiso(string $modulo, string $accion): bool
    {
        $this->permisosCache ??= $this->rol->permisos
            ->map(fn($p) => $p->modulo . '.' . $p->accion)
            ->all();

        return in_array($modulo . '.' . $accion, $this->permisosCache);
    }

    public function esCliente(): bool
    {
        return $this->cliente_id !== null;
    }


    

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class);
    }
    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class);
    }


    
}
