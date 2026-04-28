<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use App\Mail\VerifyEmail as VerifyEmailMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements MustVerifyEmail
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
        'address',
        'avatar',
        'google_id',
        'phone',
        'verification_status',
        'subscription_type',
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
    public function nursery()
    {
        return $this->hasOne(Nursery::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function sendEmailVerificationNotification(): void
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->id, 'hash' => sha1($this->email)]
        );

        Mail::to($this->email)->send(new VerifyEmailMailable($this, $url));
    }
    public function getLoginSessions()
    {
        return DB::table('sessions')
            ->where('user_id', $this->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent, // raw string, shows browser/device
                    'last_activity' => Carbon::createFromTimestamp($session->last_activity)->toDateTimeString(),
                    'session_id' => $session->id,
                ];
            });
    }

    public function getAvatarUrlAttribute(): string
    {
        if (!$this->avatar) return '';
        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) return $this->avatar;
        return route('file.view', [$this->id, $this->avatar]);
    }

    public function getAdminAvatarUrlAttribute(): string
    {
        if (!$this->avatar) return '';
        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) return $this->avatar;
        return route('admin.file.view', [$this->id, $this->avatar]);
    }
}
