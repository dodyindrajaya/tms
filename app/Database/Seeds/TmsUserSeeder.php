<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TmsUserSeeder extends Seeder
{
    public function run()
    {
        // Cari role Administrator
        $role = $this->db->table('roles')
            ->where('name', 'Administrator')
            ->get()
            ->getRowArray();

        // Kalau role Administrator belum ada, buat
        if (!$role) {
            $this->db->table('roles')->insert([
                'name' => 'Administrator',
                'description' => 'Full access to TMS application',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $roleId = $this->db->insertID();
        } else {
            $roleId = $role['id'];
        }

        // Cek apakah admin sudah ada
        $existing = $this->db->table('users')
            ->where('username', 'admin')
            ->get()
            ->getRowArray();

        if ($existing) {
            echo "User admin sudah ada. Tidak dibuat ulang.\n";
            return;
        }

        // Buat administrator
        $this->db->table('users')->insert([
            'username'     => 'admin',
            'email'        => 'admin@tms.local',
            'password_hash'=> password_hash('admin123', PASSWORD_DEFAULT),
            'role_id'      => $roleId,
            'is_active'    => 1,
            'last_login_at'=> null,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        echo "====================================\n";
        echo "TMS Administrator berhasil dibuat\n";
        echo "Username : admin\n";
        echo "Password : admin123\n";
        echo "====================================\n";
    }
}