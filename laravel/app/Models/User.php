<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use App\Services\GHLService;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'ghl_contact_id'
    ];

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
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Eventos del modelo
     */
    protected static function booted()
    {
        static::saved(function ($user) {
            
            if (!$user->email) return;

            try {
                $ghlService = new GHLService();

                $contactId = $ghlService->createOrUpdateContact([
                    'first_name' => explode(' ', $user->name)[0],
                    'last_name'  => explode(' ', $user->name)[1] ?? '',
                    'email'      => $user->email,
                    'phone'      => $user->phone ?? null,
                ]);

                if ($contactId && $contactId !== $user->ghl_contact_id) {
                    $user->ghl_contact_id = $contactId;
                    $user->saveQuietly(); 
                }
            } catch (\Exception $e) {
                Log::error('Error sincronizando usuario con GHL', [
                    'email' => $user->email,
                    'exception' => $e->getMessage()
                ]);
            }
        });
    }
}
