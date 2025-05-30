<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     * This defines how you'll call the command and its arguments/options.
     * Default values are provided for name, email, and password.
     *
     * @var string
     */
    protected $signature = 'create:admin-user
                            {name? : The name of the admin user (default: MediaMoobMessagingAdmin)}
                            {email? : The email of the admin user (default: admin@example.com)}
                            {password? : The password for the admin user (default: password)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user "MediaMoobMessagingAdmin" or update an existing one to admin.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name') ?? 'MediaMoobMessagingAdmin';
        $email = $this->argument('email') ?? 'admin@moob.com';
        $password = $this->argument('password') ?? '4dm1nM00b'; 

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'is_admin' => true, 
            ]
        );

        if ($user->wasRecentlyCreated) {
            $this->info("Admin user '{$name}' with email '{$email}' created successfully!");
        } else {
            if (!$user->is_admin) {
                $user->is_admin = true;
                $user->save();
                $this->info("User with email '{$email}' already exists and has been updated to admin!");
            } else {
                $this->info("User with email '{$email}' already exists and is already an admin.");
            }
        }

        $this->warn("Default Password for '{$email}' is: '{$password}'. Please change this password immediately in production!");
        $this->info("You can login with: Email: {$email}, Password: {$password}");
    }
}
