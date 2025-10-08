<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'estado',
        'role',
    ];
    
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'estado' => 'Activo',
        'role' => 'forwarder', // Valor por defecto
    ];
    
    // Constantes para los roles
    const ROLE_FORWARDER = 'forwarder';
    const ROLE_CARRIER = 'carrier';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    /**
     * Get all of the messages for the user.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verified' => 'boolean',
        ];
    }

    /**
     * Campos requeridos para considerar el perfil de EMPRESA completo.
     * Ajusta a tus columnas reales.
     */
    public function requiredEmpresaFields(): array
    {
        return ['nombre', 'telefono', 'direccion'];
    }

    /**
     * Devuelve un array con los campos faltantes: ["empresa.nombre", "empresa.telefono", ...]
     */
    public function missingProfileFields(): array
    {
        $missing = [];

        // Si no hay empresa, faltan todos los requeridos
        if (!$this->empresa) {
            foreach ($this->requiredEmpresaFields() as $field) {
                $missing[] = "empresa.{$field}";
            }
            return $missing;
        }

        foreach ($this->requiredEmpresaFields() as $field) {
            $val = $this->empresa->{$field} ?? null;
            if (is_null($val) || trim((string)$val) === '') {
                $missing[] = "empresa.{$field}";
            }
        }

        return $missing;
    }

    /**
     * ¿Perfil completo?
     */
    public function hasCompleteProfile(): bool
    {
        return count($this->missingProfileFields()) === 0;
    }

    public function ofertasCarga()
    {
        return $this->hasMany(OfertaCarga::class);
    }

    public function ofertasRuta()
    {
        return $this->hasMany(OfertaRuta::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function documents()
    {
        return $this->hasMany(UserDocument::class);
    }

    /**
     * Relación con la empresa que pertenece al usuario.
     */
    public function empresa()
    {
        return $this->hasOne(Empresa::class);
    }


    /**
     * La ruta para las notificaciones push.
     */
    public function routeNotificationForPush()
    {
        return $this->id;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Prueba forzada de notificación. Método para depuración.
     */
    public function testNotification()
    {
        try {
            Log::info("Enviando notificación de prueba al usuario #{$this->id}");
            $this->notify(new \App\Notifications\GenericNotification(
                "Esta es una notificación de prueba", 
                route('notifications.index')
            ));
            return true;
        } catch (\Exception $e) {
            Log::error("Error al enviar notificación de prueba: " . $e->getMessage());
            return false;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
