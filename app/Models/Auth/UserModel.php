<?php

namespace App\Models\Auth;

use App\Models\Auth\Notification\NotificationModel;
use App\Models\Auth\Notification\NotificationSettingsModel;
use App\Models\Toko\JabatanModel;
use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoUserModel;
use App\Traits\HasNotifications;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class UserModel extends Authenticatable
{
    use HasApiTokens, HasNotifications, HasFactory, Notifiable, HasRoles, SoftDeletes;
    protected $rateLimiter = 'App\\Models\\Auth\\UserModel::api';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "users";

    /**
     * The guard that the model uses.
     *
     * @var string
     */
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'username',
        'phone_number',
        'email',
        'password',
        'gender',
        'date_of_birth',
        'profile_photo_path',
        'profile_completed',
        'two_factor_enabled',
        'ktp_number',
        'ktp_name',
        'ktp_image',
        'ktp_address',
        'ktp_verified',
        'phone_verified_at',
        'address',
        'email_verified_at',
        'two_factor_enabled',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'profile_completed' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'ktp_verified' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'thumbnail',
        'initials',
        'age',
        'is_verified',
        'is_profile_completed',
        'last_active_at',
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
    }

    public function getNotificationCount(string $status): int
    {
        return NotificationModel::where(function ($query) {
            $query->where(function ($q) {
                $q->where('notifiable_type', 'App\Models\Auth\UserModel')
                    ->where('notifiable_id', $this->id);
            })->orWhere(function ($q) {
                $q->where('notifiable_type', 'App\Models\Role')
                    ->where('notifiable_id', $this->roles->first()->name ?? 'guest');
            });
        })
            ->where('status', $status)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Get the user's initials.
     *
     * @return string
     */
    public function getInitialsAttribute()
    {
        if (!$this->name) {
            return 'U';
        }

        $words = explode(' ', $this->name);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
            if (strlen($initials) >= 2) break;
        }

        return $initials ?: strtoupper(substr($this->name, 0, 2));
    }

    public function getUserPhoto()
    {
        $initials = $this->getInitialsAttribute();
        return $this->profile_photo_path . "https://ui-avatars.com/api/?name=" . $initials . "&color=FFF&background=FF0000";
    }

    /**
     * Get the user's age.
     *
     * @return int|null
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? Carbon::parse($this->date_of_birth)->age : null;
    }

    /**
     * Get the user's profile photo URL.
     *
     * @return string
     */
    public function getThumbnailAttribute()
    {
        if ($this->profile_photo_path) {
            return Storage::url($this->profile_photo_path);
        }

        if ($this->custom_avatar) {
            return $this->getUserPhoto();
        }

        return $this->getUserPhoto();
    }

    /**
     * Get the user's verification status.
     *
     * @return bool
     */
    public function getIsVerifiedAttribute()
    {
        return $this->isEmailVerified() && $this->isPhoneVerified();
    }

    public function getLastActiveAtAttribute()
    {
        if (!$this->deviceVerifications()->whereNotNull('last_active_at')->exists()) {
            return null;
        }
        return $this->deviceVerifications()
            ->whereNotNull('last_active_at')
            ->orderBy('last_active_at', 'desc')
            ->first()
            ->last_active_at;
    }

    /**
     * Check if user's phone is verified.
     *
     * @return bool
     */
    public function isPhoneVerified()
    {
        return !is_null($this->phone_verified_at);
    }

    /**
     * Check if user's email is verified.
     *
     * @return bool
     */
    public function isEmailVerified()
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Get the devices for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deviceVerifications()
    {
        return $this->hasMany(UserDeviceModel::class, 'user_id');
    }

    /**
     * Get all devices for the user (alias for deviceVerifications).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices()
    {
        return $this->hasMany(UserDeviceModel::class, 'user_id');
    }

    /**
     * Get all FCM tokens for the user's devices.
     *
     * @return array
     */
    public function fcmTokens()
    {
        return $this->devices()
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();
    }

    /**
     * Get the verified devices for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function verifiedDevices()
    {
        return $this->deviceVerifications()->where('verified_at', '!=', null);
    }

    /**
     * Get and format the user's most recent activity timestamp
     *
     * @return string
     */
    public function getLastActiveFormatted()
    {
        $lastActiveDevice = $this->deviceVerifications()
            ->whereNotNull('last_active_at')
            ->orderBy('last_active_at', 'desc')
            ->first();

        if (!$lastActiveDevice) {
            return 'Never';
        }

        $lastActive = $lastActiveDevice->last_active_at;
        $now = Carbon::now();

        $diffInSeconds = $now->diffInSeconds($lastActive);
        $diffInMinutes = $now->diffInMinutes($lastActive);
        $diffInDays = $now->diffInDays($lastActive);

        Carbon::setLocale('id');

        if ($diffInSeconds < 60) {
            return 'Online';
        }

        if ($diffInMinutes < 60) {
            return $diffInMinutes . ' menit yang lalu';
        }

        if ($lastActive->isToday()) {
            return 'Hari Ini, ' . $lastActive->format('H:i A');
        }

        if ($lastActive->isYesterday()) {
            return 'Kemarin, ' . $lastActive->format('H:i A');
        }

        if ($diffInDays < 7) {
            return $lastActive->translatedFormat('l, H:i A');
        }

        return $lastActive->translatedFormat('l, j F Y');
    }

    /**
     * Get smart formatted last active time
     *
     * @return string
     */
    public function getLastActiveAt()
    {
        $lastActive = $this->deviceVerifications()
            ->whereNotNull('last_active_at')
            ->orderBy('last_active_at', 'desc')
            ->first();

        if (!$lastActive) {
            return 'Never';
        }

        $now = Carbon::now();
        $diffInSeconds = $now->diffInSeconds($lastActive);
        $diffInMinutes = $now->diffInMinutes($lastActive);
        $diffInHours = $now->diffInHours($lastActive);
        $diffInDays = $now->diffInDays($lastActive);

        Carbon::setLocale('id');

        if ($diffInSeconds < 60) {
            return 'Online';
        }

        if ($diffInMinutes < 60) {
            return $diffInMinutes . ' menit yang lalu';
        }

        if ($lastActive->isToday()) {
            return 'Hari Ini, ' . $lastActive->format('H:i A');
        }

        if ($lastActive->isYesterday()) {
            return 'Kemarin, ' . $lastActive->format('H:i A');
        }

        if ($diffInDays < 7) {
            return $lastActive->translatedFormat('l, H:i A');
        }

        return $lastActive->translatedFormat('l, j F Y');
    }

    /**
     * Get the stores associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tokos()
    {
        return $this->belongsToMany(TokoModel::class, 'toko_user', 'user_id', 'toko_id')
            ->withPivot('jabatan_id')
            ->withTimestamps()
            ->using(TokoUserModel::class);
    }

    /**
     * Get the stores owned by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ownedTokos()
    {
        return $this->hasMany(TokoModel::class, 'owner_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(JabatanModel::class, 'jabatan_id');
    }

    /**
     * Get list of required fields for profile completion
     *
     * @return array
     */
    public static function getRequiredProfileFields(): array
    {
        return [
            'name',
            'phone_verified_at',
            'address',
            'ktp_number',
            'ktp_image',
            'ktp_name',
            'ktp_address',
            'gender',
            'date_of_birth',
        ];
    }

    /**
     * Get profile completion status with details
     *
     * @return array
     */
    public function getProfileCompletionDetails(): array
    {
        $requiredFields = self::getRequiredProfileFields();
        $completionStatus = [];

        foreach ($requiredFields as $field) {
            $completionStatus[$field] = !empty($this->{$field});
        }

        $completedCount = collect($completionStatus)->filter()->count();
        $totalFields = count($requiredFields);

        return [
            'completed' => $completedCount === $totalFields,
            'completion_percentage' => round(($completedCount / $totalFields) * 100),
            'fields' => $completionStatus,
        ];
    }

    /**
     * Check if the user's profile is completed.
     *
     * @return bool
     */
    public function isProfileCompleted(): bool
    {
        $requiredFields = self::getRequiredProfileFields();

        foreach ($requiredFields as $field) {
            if (empty($this->{$field})) {
                return false;
            }
        }

        return true;
    }

    public function getIsProfileCompletedAttribute()
    {
        return $this->isProfileCompleted();
    }

    /**
     * Update profile completion status.
     *
     * @return void
     */
    public function updateProfileCompletionStatus()
    {
        $this->profile_completed = $this->isProfileCompleted();
        $this->save();
    }

    /**
     * Mark user's phone as verified.
     *
     * @return bool
     */
    public function markPhoneAsVerified()
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Set user's random avatar.
     *
     * @return void
     */
    public function setRandomAvatar()
    {
        $this->getRandomAvatar();
    }

    /**
     * Get gender display name.
     *
     * @return string|null
     */
    public function getGenderDisplayAttribute()
    {
        if (!$this->gender) {
            return null;
        }

        return [
            'male' => 'Laki-laki',
            'female' => 'Perempuan',
            'other' => 'Lainnya',
        ][$this->gender] ?? $this->gender;
    }

    /**
     * Hash the password before saving.
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value) && !Str::startsWith($value, '$2y$')) {
            $this->attributes['password'] = bcrypt($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Get a consistent gradient class for this user
     * 
     * @return string
     */
    public function getAvatarGradientClass()
    {
        $gradients = [
            'male' => [
                'bg-gradient-to-r from-blue-500 to-cyan-500',
                'bg-gradient-to-r from-blue-600 to-indigo-500',
                'bg-gradient-to-r from-indigo-500 to-purple-500',
                'bg-gradient-to-r from-sky-500 to-indigo-500',
                'bg-gradient-to-r from-blue-500 to-teal-500',
            ],
            'female' => [
                'bg-gradient-to-r from-pink-500 to-rose-500',
                'bg-gradient-to-r from-purple-500 to-pink-500',
                'bg-gradient-to-r from-fuchsia-500 to-pink-500',
                'bg-gradient-to-r from-rose-500 to-red-500',
                'bg-gradient-to-r from-pink-500 to-orange-500',
            ],
            'other' => [
                'bg-gradient-to-r from-emerald-500 to-teal-500',
                'bg-gradient-to-r from-amber-500 to-orange-500',
                'bg-gradient-to-r from-lime-500 to-green-500',
                'bg-gradient-to-r from-violet-500 to-purple-500',
                'bg-gradient-to-r from-yellow-500 to-amber-500',
            ]
        ];

        $gender = $this->gender ?? 'other';
        $genderGradients = $gradients[$gender];
        $index = $this->id % count($genderGradients);

        return $genderGradients[$index];
    }

    /**
     * Get avatar HTML with initials and gender-based gradient background
     * 
     * @param int $size
     * @return string
     */
    public function getAvatarHtml($size = 10)
    {
        $gradientClass = $this->getAvatarGradientClass();

        return '<div class="' . $gradientClass . ' flex h-' . $size . ' w-' . $size . ' items-center justify-center rounded-full font-medium text-white">' . $this->initials . '</div>';
    }
}
