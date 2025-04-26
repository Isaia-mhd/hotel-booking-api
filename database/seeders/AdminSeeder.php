<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(!User::where("email", config('admin.email'))->exists())
        {
            User::create([
                "name" => config('admin.name'),
                "email" => config('admin.email'),
                "phone" => config('admin.phone'),
                "email_verified_at" => now(),
                "password" => Hash::make(config('admin.password')),
                "role" => "admin"
            ]);
        }
    }
}
