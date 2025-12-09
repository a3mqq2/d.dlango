<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Cache for user permissions
     */
    protected ?array $permissionsCache = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get role label
     */
    public function getRoleLabel(): string
    {
        $roles = [
            'admin' => app()->getLocale() === 'ar' ? 'مدير' : 'Admin',
            'user' => app()->getLocale() === 'ar' ? 'مستخدم' : 'User',
        ];

        return $roles[$this->role] ?? $this->role;
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for admins
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope for regular users
     */
    public function scopeUsers($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Get transactions created by this user
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get user permissions
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        // Admins have all permissions
        if ($this->isAdmin()) {
            return true;
        }

        // Load permissions into cache if not loaded
        if ($this->permissionsCache === null) {
            $this->permissionsCache = $this->permissions()->pluck('name')->toArray();
        }

        return in_array($permission, $this->permissionsCache);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sync user permissions
     */
    public function syncPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
        $this->permissionsCache = null; // Clear cache
    }

    /**
     * Get permission names as array
     */
    public function getPermissionNames(): array
    {
        return $this->permissions()->pluck('name')->toArray();
    }
}
