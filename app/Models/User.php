<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'github_db_id',
        'github_repositories_count',
        'github_subscribers_count',
        'profile_shown_counter',
        'github_joined_at',
        'location',
        'avatar_url',
        'bio',
        'created_at',
    ];


    public function setGithubJoinedAtAttribute($value)
    {
        try {
            logger(Carbon::parse($value)->toDateTimeString());
            return Carbon::parse($value);
        } catch (\Exception $exception){
            //handle error
            return null;
        }
    }

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
    ];

    /**
     * @return HasMany
     */
    public function repositories(): HasMany
    {
        return $this->hasMany(GithubRepository::class);
    }
}
