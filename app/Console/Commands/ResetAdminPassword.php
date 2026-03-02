<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:reset-password {--email=admin@clothr.com} {--password=password123}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the admin user password';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');

        // Find user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        // Update password and role
        $user->update([
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $this->info("Admin user updated successfully!");
        $this->info("Email: {$email}");
        $this->info("Password: {$password}");
        $this->info("Role: admin");
        $this->info("🔗 You can now login at: http://127.0.0.1:8000/login");

        return 0;
    }
}
