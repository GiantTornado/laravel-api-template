<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\RolesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['email', 'password', 'email_verified_at', 'avatar', 'role_id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the profile associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile() {
        // return $this->hasOne(RelatedModel::class, 'foreign_key_in_related_model', 'primary_key_in_current_model');
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    public function socialAccounts() {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * Get the role that the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role() {
        // return $this->belongsTo(RelatedModel::class, 'foreign_key_in_current_model', 'primary_key_in_related_model')->chained_methods;
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
