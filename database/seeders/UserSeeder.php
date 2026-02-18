<?php

namespace Database\Seeders;

use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Диспетчер
        User::create([
            'name' => 'Диспетчер Иванов',
            'email' => 'dispatcher@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_DISPATCHER,
        ]);

        // Мастера
        User::create([
            'name' => 'Мастер Петров',
            'email' => 'master1@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_MASTER,
        ]);

        User::create([
            'name' => 'Мастер Сидоров',
            'email' => 'master2@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_MASTER,
        ]);

        $this->command->info('Созданы пользователи:');
        $this->command->info('  Диспетчер: dispatcher@example.com / password');
        $this->command->info('  Мастер 1: master1@example.com / password');
        $this->command->info('  Мастер 2: master2@example.com / password');
    }
}
