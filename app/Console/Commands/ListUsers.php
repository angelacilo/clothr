<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users in the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::all()->map(function ($user) {
            return [
                'ID' => $user->user_id,
                'Name' => $user->name,
                'Email' => $user->email,
                'Role' => $user->role ?? 'N/A',
                'Created' => $user->created_at,
            ];
        });

        if ($users->isEmpty()) {
            $this->info("No users found in database!");
            return 0;
        }

        $this->table(['ID', 'Name', 'Email', 'Role', 'Created'], $users);

        return 0;
    }
}
