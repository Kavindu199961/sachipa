<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'sachipa',
            'email' => 'sachipa@gmail.com',
            'password' => Hash::make('123456789'),
        ]);
        
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('test123'),
        ]);
    }
}