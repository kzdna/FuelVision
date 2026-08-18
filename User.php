<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';

    public $timestamps = true;

    protected $fillable = [
        'role_id',
        'nama',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    // users table has no remember_token column in the source SQL, so
    // "remember me" login is intentionally not supported.
    public function getRememberTokenName(): ?string
    {
        return null;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function transaksiPengisianBbm(): HasMany
    {
        return $this->hasMany(TransaksiPengisianBbm::class);
    }

    public function isRole(string $roleName): bool
    {
        return $this->role && $this->role->nama_role === $roleName;
    }
}
