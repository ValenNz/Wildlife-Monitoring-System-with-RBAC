<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SetupRbacComplete extends Migration
{
    public function up()
    {
        // 1. Buat tabel roles (jika belum ada)
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. Buat tabel permissions (jika belum ada)
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // 3. Buat tabel role_permission (jika belum ada)
        if (!Schema::hasTable('role_permission')) {
            Schema::create('role_permission', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('permission_id');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
                $table->unique(['role_id', 'permission_id']);
                $table->timestamps();
            });
        }

        // 4. Tambahkan role_id ke users (jika belum ada)
        if (!Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id')->nullable()->after('remember_token');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            });
        }

        // 5. Insert data default (hanya jika tabel kosong)
        if (DB::table('roles')->count() == 0) {
            $this->insertDefaultRoles();
        }
        if (DB::table('permissions')->count() == 0) {
            $this->insertDefaultPermissions();
        }
        if (DB::table('role_permission')->count() == 0) {
            $this->assignRolePermissions();
        }

        // 6. Update users dengan role_id
        $this->assignRolesToUsers();
    }

    private function insertDefaultRoles()
    {
        DB::table('roles')->insert([
            ['name' => 'Administrator', 'description' => 'Full system access'],
            ['name' => 'Konservasionis Lapangan', 'description' => 'Field monitoring'],
            ['name' => 'Peneliti Ekologi', 'description' => 'Data analysis'],
            ['name' => 'Pengambil Kebijakan', 'description' => 'Strategic reports']
        ]);
    }

    private function insertDefaultPermissions()
    {
        $permissions = [
            ['name' => 'view_dashboard', 'description' => 'View dashboard'],
            ['name' => 'manage_devices', 'description' => 'Manage GPS devices'],
            ['name' => 'manage_geozones', 'description' => 'Manage risk zones'],
            ['name' => 'view_reports', 'description' => 'View reports'],
            ['name' => 'generate_reports', 'description' => 'Generate reports'],
            ['name' => 'view_notifications', 'description' => 'View notifications'],
            ['name' => 'view_activity_logs', 'description' => 'View activity logs'],
            ['name' => 'export_data', 'description' => 'Export data'],
            ['name' => 'view_map', 'description' => 'View live map']
        ];
        DB::table('permissions')->insert($permissions);
    }

    private function assignRolePermissions()
    {
        $roles = DB::table('roles')->pluck('id', 'name')->toArray();
        $permissions = DB::table('permissions')->pluck('id', 'name')->toArray();

        $rolePermissions = [];

        // Administrator - semua permission
        foreach ($permissions as $perm) {
            $rolePermissions[] = ['role_id' => $roles['Administrator'], 'permission_id' => $perm];
        }

        // Konservasionis Lapangan
        $conservatorPerms = ['manage_devices', 'manage_geozones', 'view_notifications', 'view_map'];
        foreach ($conservatorPerms as $permName) {
            $rolePermissions[] = ['role_id' => $roles['Konservasionis Lapangan'], 'permission_id' => $permissions[$permName]];
        }

        // Peneliti Ekologi
        $researcherPerms = ['view_reports', 'generate_reports', 'view_notifications', 'view_map'];
        foreach ($researcherPerms as $permName) {
            $rolePermissions[] = ['role_id' => $roles['Peneliti Ekologi'], 'permission_id' => $permissions[$permName]];
        }

        // Pengambil Kebijakan
        $decisionMakerPerms = ['view_dashboard', 'view_reports', 'generate_reports', 'view_map'];
        foreach ($decisionMakerPerms as $permName) {
            $rolePermissions[] = ['role_id' => $roles['Pengambil Kebijakan'], 'permission_id' => $permissions[$permName]];
        }

        DB::table('role_permission')->insert($rolePermissions);
    }

    private function assignRolesToUsers()
    {
        $roles = DB::table('roles')->pluck('id', 'name')->toArray();

        DB::table('users')->where('email', 'admin@example.com')->update(['role_id' => $roles['Administrator']]);
        DB::table('users')->where('email', 'editor@example.com')->update(['role_id' => $roles['Konservasionis Lapangan']]);
        DB::table('users')->where('email', 'peneliti@example.com')->update(['role_id' => $roles['Peneliti Ekologi']]);
        DB::table('users')->where('email', 'viewer@example.com')->update(['role_id' => $roles['Pengambil Kebijakan']]);
    }

    public function down()
    {
        // Hapus foreign key dulu
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropForeign(['role_id']);
            }
        });

        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');

        // Hapus kolom role_id
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropColumn('role_id');
            }
        });
    }
}
