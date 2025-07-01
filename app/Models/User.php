<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'language',
        'role',
        'google_id',
        'phone',
        'created_by',
        'created_by_user_id',
        'fcm_token'
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
    public function isPatient()
    {
        return $this->role === 'patient';
    }
    public function isDoctor()
    {
        return $this->role === 'doctor';
    }

    public function isSecretary()
    {
        return $this->role === 'secretary';
    }
    public function patient()
    {
        return $this->hasOne(Patient::class, 'user_id');

    }
    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'user_id');

    }
      public function secretary()
    {
        return $this->hasOne(Secretary::class, 'user_id');

    }
    // الشخص الذي أنشأ هذا المستخدم
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // الأشخاص الذين أنشأهم هذا المستخدم
    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by_user_id');
    }

    // فحص هل الحساب تم إنشاؤه من شخص آخر
    public function wasCreatedByAnother()
    {
        return !is_null($this->created_by);
    }
}
