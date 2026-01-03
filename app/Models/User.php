<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB; // 🔥 Tambahkan ini!

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
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
        'role_id', // FK ke roles
        'remember_token'
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

    public function canAccess(string $permission): bool
    {
        if (!$this->role_id) {
            return false;
        }

        return DB::table('role_permission')
            ->join('permissions', 'role_permission.permission_id', '=', 'permissions.id')
            ->where('role_permission.role_id', $this->role_id)
            ->where('permissions.name', $permission)
            ->exists();
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions()
    {
        return $this->hasManyThrough(Permission::class, Role::class, 'id', 'role_id', 'role_id', 'id');
    }

    // Relasi ke Notifications (penerima)
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function generatedReports()
    {
        return $this->hasMany(Report::class, 'generated_by');
    }

    public function assignedIncidents()
        {
            return $this->hasMany(Incident::class, 'assigned_to');
        }

        use Notifiable;


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    }

    public function hasPermission($permissionName)
    {
        foreach ($this->roles as $role) {
            foreach ($role->permissions as $permission) {
                if ($permission->name === $permissionName) {
                    return true;
                }
            }
        }
        return false;
    }

}
