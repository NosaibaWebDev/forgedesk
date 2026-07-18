<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin
                            {--name= : Admin display name}
                            {--email= : Admin email}
                            {--password= : Admin password (auto-generated if omitted)}';

    protected $description = 'Create the first admin user for production';

    public function handle(): int
    {
        if (User::where('role', 'admin')->exists()) {
            if (! $this->confirm('An admin user already exists. Create another?')) {
                return self::SUCCESS;
            }
        }

        $name = $this->option('name') ?: $this->ask('Admin name', 'מנהל המערכת');
        $email = $this->option('email') ?: $this->ask('Admin email');

        if (! $email) {
            $this->error('Email is required.');
            return self::FAILURE;
        }

        $password = $this->option('password') ?: Str::random(16);

        $admin = new User();
        $admin->forceFill([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'is_active' => true,
        ]);
        $admin->save();

        $this->info('Admin user created.');
        $this->warn("Email: {$email}");
        $this->warn("Password: {$password}");
        $this->warn('Save these credentials securely.');

        return self::SUCCESS;
    }
}
