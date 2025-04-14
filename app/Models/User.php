<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Afsakar\FilamentOtpLogin\Models\Contracts\CanLoginDirectly;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements CanLoginDirectly
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

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

             // If no password is provided and no phone number, use a combination of user_id and unique_id as the password
             if (empty($user->password)) {
                $user->password = Hash::make($user->uuid); // Combine user_id and unique_id for the password
            }

            if (empty($user->email)) {
                // Attempt to create an email based on user's name, fallback to unique_id
                $username = $user->name
                    ? strtolower(str_replace(' ', '.', $user->name))
                    : strtolower($user->uuid);

                // Initial email format
                $email = $username . '@' . env('DOMAIN', 'shaheen.com'); // Use your domain

                // Check if email exists, if so, make it unique by appending a number
                $counter = 1;
                $uniqueEmail = $email;

                while (self::where('email', $uniqueEmail)->exists()) {
                    $uniqueEmail = $username . $counter . '@' . env('DOMAIN', 'shaheen.com');
                    $counter++;
                }

                // Assign the unique email
                $user->email = $uniqueEmail;
            }

            // store_id is auth user store id if store_id not set
            if (empty($user->store_id)) {
                $user->store_id = auth()->user()?->store?->id ?? null;
            }

        });

        static::created(function ($user) {
            self::createWalletForAFN($user->id);
        });
    }




    private static function createWalletForAFN($id)
        {
            // Find the currency ID for 'AFN'
            $currency = Currency::where('code', 'AFN')->first();

            if (!$currency) {
                return response()->json(['error' => 'Currency with code AFN not found'], 404);
            }


            //when user created also created for this user wallet
           Wallet::create([
                // 'uuid' => self::generateUniqueCodeForWallet(), // Generate a unique UUID
                'owner_type' => 'App\Models\User', // Specify the related model , this is morfh relationship
                'owner_id' => $id, // Assuming the authenticated user
                'balance' => 0.00, // Default balance
                'currency_id' => $currency->id, // Assign the found currency ID
                'status' => 'active',
            ]);

        }



    private static function generateUniqueCode()
    {
        $letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; // Avoids confusing characters
        $numbers = '0123456789'; // Ensures proper numeric flow

        // Code Format: UXX08239
        $code = 'C' . substr(str_shuffle($letters), 0, 2) . random_int(10000, 99999);

        // Ensure Uniqueness
        while (self::where('uuid', $code)->exists()) {
            $code = 'C' . substr(str_shuffle($letters), 0, 2) . random_int(10000, 99999);
        }

        return $code;
    }

    public function kyc() {
        return $this->hasOne(KYC::class);
    }

    public function wallets() {
        return $this->hasMany(Wallet::class, 'owner_id')
            ->where('owner_type', 'App\Models\User');
    }

    // which store related this customer is using store_id in users table
    // one to one

     public function storeRelatedTo() {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function store() {
        return $this->hasOne(Store::class);
    }

    public function banks()
    {
        return $this->hasMany(BankAccount::class, 'user_id')
          ; // 👈 Filter only customer accounts
    }

    public function createdHawlas()
    {
        return $this->hasMany(Hawla::class, 'created_by')
          ; // 👈 Filter only customer accounts
    }




}
