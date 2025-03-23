<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Afsakar\FilamentOtpLogin\Models\Contracts\CanLoginDirectly;
use Illuminate\Database\Eloquent\SoftDeletes;
class User extends Authenticatable implements CanLoginDirectly
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public function canLoginDirectly(): bool
    {
        return true;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

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
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = self::generateUniqueCode();
            }
        });
    }

    private static function generateUniqueCode()
    {
        $letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; // Avoids confusing characters
        $numbers = '0123456789'; // Ensures proper numeric flow

        // Code Format: UXX08239
        $code = 'U' . substr(str_shuffle($letters), 0, 2) . random_int(10000, 99999);

        // Ensure Uniqueness
        while (self::where('uuid', $code)->exists()) {
            $code = 'U' . substr(str_shuffle($letters), 0, 2) . random_int(10000, 99999);
        }

        return $code;
    }



    public function kyc() {
        return $this->hasOne(KYC::class);
    }
}
