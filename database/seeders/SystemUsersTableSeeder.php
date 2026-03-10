<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SystemUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define multiple admin users
        $admins = [
            [
                'custom_id' => 'ADM001',
                'fname' => 'System',
                'lname' => 'Administrator',
                'email' => 'admin@nexorait.lk',
                'mobile' => '0711234567',
                'nic' => '123456789V',
                'bday' => '1985-01-15',
                'gender' => 'male',
                'address1' => 'Mirigama,Sri Lanka',
                'address2' => 'Nexora IT Solutions',
                'address3' => 'Mirigama',
            ],
            [
                'custom_id' => 'ADM002',
                'fname' => 'System',
                'lname' => 'Administrator',
                'email' => 'yeseducation@gmail.com',
                'mobile' => '0719876543',
                'nic' => '987654321V',
                'bday' => '1990-05-20',
                'gender' => 'female',
                'address1' => 'Wariyapola',
                'address2' => 'Sri Lanka',
                'address3' => 'Sri Lanka',
            ],
            // Add more admins here
        ];

        foreach ($admins as $admin) {
            // Create or get the admin user account
            $user = $this->createAdminUser($admin['email']);

            // Check if the system user already exists
            $existingSystemUser = DB::table('system_users')
                ->where('email', $admin['email'])
                ->orWhere('custom_id', $admin['custom_id'])
                ->first();

            if (!$existingSystemUser) {
                DB::table('system_users')->insert([
                    'custom_id' => $admin['custom_id'],
                    'user_id' => $user->id,
                    'fname' => $admin['fname'],
                    'lname' => $admin['lname'],
                    'email' => $admin['email'],
                    'mobile' => $admin['mobile'],
                    'nic' => $admin['nic'],
                    'bday' => $admin['bday'],
                    'gender' => $admin['gender'],
                    'address1' => $admin['address1'],
                    'address2' => $admin['address2'],
                    'address3' => $admin['address3'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("✅ Admin {$admin['email']} created successfully.");
            } else {
                $this->command->info("ℹ️ Admin {$admin['email']} already exists.");
            }
        }
    }

    /**
     * Create the admin user in the users table.
     */
    private function createAdminUser(string $email)
    {
        // Get or create Admin user type
        $adminType = DB::table('user_types')->where('type', 'Admin')->first();
        $adminTypeId = $adminType ? $adminType->id : DB::table('user_types')->insertGetId([
            'type' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Check if user exists
        $user = DB::table('users')->where('email', $email)->first();

        if (!$user) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'System Administrator',
                'email' => $email,
                'password' => Hash::make('yes@Admin'), // default password
                'user_type' => $adminTypeId,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return (object)['id' => $userId];
        }

        return $user;
    }
}