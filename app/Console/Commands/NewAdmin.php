<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class NewAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask("Admin name: ");
        $email = $this->ask("Admin email: ");
        $password = $this->secret("Admin password: ");
        $phone = $this->ask("Admin phone number: ");
        $confirm = $this->ask("Enter security code to proceed: ");

        // Check if user exists
        if(User::where('email', $email)->exists())
        {
            $this->error('A user with this email already exists.');
            return 1;
        }

        if($confirm !== config("app.admin_setup_code"))
        {
            $this->error('Wrong code. Aborting.');
            return 1;
        }

        User::create([
            "name" => $name,
            "email" => $email,
            "password" => Hash::make($password),
            "phone" => $phone,
            "role" => "admin"
        ]);

        $this->info("✅ Admin user created successfully!");
        return 0;
    }
}
